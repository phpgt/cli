<?php
namespace Gt\Cli\Command;

use Gt\Cli\Argument\Argument;
use Gt\Cli\Argument\ArgumentList;
use Gt\Cli\Argument\ArgumentValueList;
use Gt\Cli\Argument\CommandArgument;
use Gt\Cli\Argument\NamedArgument;
use Gt\Cli\Argument\NotEnoughArgumentsException;
use Gt\Cli\Parameter\MissingRequiredParameterException;
use Gt\Cli\Parameter\MissingRequiredParameterValueException;
use Gt\Cli\Parameter\NamedParameter;
use Gt\Cli\Parameter\Parameter;
use Gt\Cli\Parameter\UserParameter;
use Gt\Cli\Stream;

abstract class Command {
	/** @var Stream */
	protected $output;

	public function setOutput(Stream $output = null) {
		$this->output = $output;
	}

	abstract public function getName():string;

	abstract public function getDescription():string;

	/** @return  NamedParameter[] */
	abstract public function getRequiredNamedParameterList():array;

	/** @return  NamedParameter[] */
	abstract public function getOptionalNamedParameterList():array;

	/** @return  Parameter[] */
	abstract public function getRequiredParameterList():array;

	/** @return  Parameter[] */
	abstract public function getOptionalParameterList():array;

	abstract public function run(ArgumentValueList $arguments = null):void;

	public function getUsage():string {
		$message = "";

		$message .= "Usage: ";
		$message .= $this->getName();

		foreach($this->getRequiredNamedParameterList() as $parameter) {
			$message .= " ";
			$message .= $parameter->getOptionName();
		}

		foreach($this->getOptionalNamedParameterList() as $parameter) {
			$message .= " [";
			$message .= $parameter->getOptionName();
			$message .= "]";
		}

		foreach($this->getRequiredParameterList() as $parameter) {
			$message .= " --";
			$message .= $parameter->getLongOption();

			if($short = $parameter->getShortOption()) {
				$message .= "|-$short";
			}

			if($parameter->takesValue()) {
				$message .= " ";
				$message .= $parameter->getExample();
			}
		}

		foreach($this->getOptionalParameterList() as $parameter) {
			$message .= " [--";
			$message .= $parameter->getLongOption();

			if($short = $parameter->getShortOption()) {
				$message .= "|-$short";
			}

			if($parameter->takesValue()) {
				$message .= " ";
				$message .= $parameter->getExample();
			}

			$message .= "]";
		}

		return $message;
	}

	public function checkArguments(ArgumentList $argumentList):void {
		$numRequiredNamedParameters = count(
			$this->getRequiredNamedParameterList()
		);

		$passedNamedArguments = 0;
		foreach($argumentList as $argument) {
			if($argument instanceof NamedArgument) {
				$passedNamedArguments ++;
			}
		}

		if($passedNamedArguments < $numRequiredNamedParameters) {
			throw new NotEnoughArgumentsException(
				"Passed: $passedNamedArguments "
				. "required: $numRequiredNamedParameters"
			);
		}

		foreach($this->getRequiredParameterList() as $parameter) {
			if(!$argumentList->contains($parameter)) {
				throw new MissingRequiredParameterException(
					$parameter
				);
			}

			if($parameter->takesValue()) {
				$value = $argumentList->getValueForParameter(
					$parameter
				);
				if(is_null($value)) {
					throw new MissingRequiredParameterValueException(
						$parameter
					);
				}
			}
		}
	}

	public function getArgumentValueList(
		ArgumentList $arguments
	):ArgumentValueList {
		$namedParameterIndex = 0;
		/** @var NamedParameter[] */
		$namedParameterList = array_merge(
			$this->getRequiredNamedParameterList(),
			$this->getOptionalNamedParameterList()
		);

		/** @var Parameter[] $parameterList */
		$parameterList = array_merge(
			$this->getRequiredParameterList(),
			$this->getOptionalParameterList()
		);

		$argumentValueList = new ArgumentValueList();

		foreach($arguments as $argument) {
			if($argument instanceof CommandArgument) {
				continue;
			}
			elseif($argument instanceof NamedArgument) {
				/** @var NamedParameter $parameter */
				$parameter = $namedParameterList[
					$namedParameterIndex
				] ?? null;

				if(is_null($parameter)) {
					$argumentValueList->set(
						Argument::USER_DATA,
						$argument->getValue()
					);
				}
				else {
					$argumentValueList->set(
						$parameter->getOptionName(),
						$argument->getValue()
					);
				}

				$namedParameterIndex++;
			}
			elseif($argument instanceof Argument) {
				/** @var Parameter|null $parameter */
				$parameter = null;

				foreach($parameterList as $parameterToCheck) {
					$argumentKey = $argument->getKey();
					if($argumentKey === $parameterToCheck->getLongOption()
					|| $argumentKey === $parameterToCheck->getShortOption()) {
						$parameter = $parameterToCheck;
						break;
					};
				}

				if(is_null($parameter)) {
					$parameter = new UserParameter(
						!empty($argument->getValue()),
						$argument->getKey()
					);
				}

				$argumentValueList->set(
					$parameter->getLongOption(),
					$argument->getValue()
				);
			}
		}

		return $argumentValueList;
	}

	protected function write(
		string $message,
		string $streamName = Stream::OUT
	):void {
		if(is_null($this->output)) {
			return;
		}

		$this->output->write($message, $streamName);
	}

	protected function writeLine(
		string $message = "",
		string $streamName = Stream::OUT
	):void {
		$this->write($message . PHP_EOL, $streamName);
	}
}