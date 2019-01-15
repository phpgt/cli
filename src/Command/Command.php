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
use Gt\Cli\Stream;

abstract class Command {
	/** @var Stream */
	protected $output;

	protected $name;
	protected $description = "";
	/** @var NamedParameter[] */
	protected $optionalNamedParameterList = [];
	/** @var NamedParameter[] */
	protected $requiredNamedParameterList = [];
	/** @var Parameter[] */
	protected $optionalParameterList = [];
	/** @var Parameter[] */
	protected $requiredParameterList = [];

	public function setOutput(Stream $output = null) {
		$this->output = $output;
	}

	abstract public function run(ArgumentValueList $arguments):void;

	public function getName():string {
		return $this->name;
	}

	protected function setName(string $name):void {
		$this->name = $name;
	}

	public function getDescription():string {
		return $this->description;
	}

	protected function setDescription(string $description):void {
		$this->description = $description;
	}


	public function getUsage():string {
		$message = "";

		$message .= "Usage: ";
		$message .= $this->getName();

		foreach($this->requiredNamedParameterList as $parameter) {
			$message .= " ";
			$message .= $parameter->getOptionName();
		}

		foreach($this->optionalNamedParameterList as $parameter) {
			$message .= " [";
			$message .= $parameter->getOptionName();
			$message .= "]";
		}

		foreach($this->requiredParameterList as $parameter) {
			$message .= " --";
			$message .= $parameter->getLongOption();

			if($short = $parameter->getShortOption()) {
				$message .= "|-$short";
			}

			if($parameter->isValueRequired()) {
				$message .= " ";
				$message .= $parameter->getExample();
			}
		}

		foreach($this->optionalParameterList as $parameter) {
			$message .= " [--";
			$message .= $parameter->getLongOption();

			if($short = $parameter->getShortOption()) {
				$message .= "|-$short";
			}

			if($parameter->isValueRequired()) {
				$message .= " ";
				$message .= $parameter->getExample();
			}

			$message .= "]";
		}

		return $message;
	}

	public function checkArguments(ArgumentList $argumentList):void {
		$numRequiredNamedParameters = count(
			$this->requiredNamedParameterList
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

		foreach($this->requiredParameterList as $parameter) {
			if(!$argumentList->contains($parameter)) {
				throw new MissingRequiredParameterException(
					$parameter
				);
			}

			if($parameter->isValueRequired()) {
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

	/**
	 * @return NamedParameter[]
	 */
	public function getRequiredNamedParameterList():array {
		return $this->requiredNamedParameterList;
	}

	/**
	 * @return NamedParameter[]
	 */
	public function getOptionalNamedParameterList():array {
		return $this->optionalNamedParameterList;
	}

	protected function setRequiredNamedParameter(string $name):void {
		$this->requiredNamedParameterList []= new NamedParameter(
			$name
		);
	}

	/**
	 * @return Parameter[]
	 */
	public function getOptionalParameterList():array {
		return $this->optionalParameterList;
	}

	protected function setOptionalNamedParameter(string $name):void {
		$this->optionalNamedParameterList []= new NamedParameter(
			$name
		);
	}

	protected function setOptionalParameter(
		bool $requireValue,
		string $longOption,
		string $shortOption = null,
		string $example = null
	):void {
		$this->optionalParameterList []= new Parameter(
			$requireValue,
			$longOption,
			$shortOption,
			$example
		);
	}

	/**
	 * @return Parameter[]
	 */
	public function getRequiredParameterList():array {
		return $this->requiredParameterList;
	}

	protected function setRequiredParameter(
		bool $requireValue,
		string $longOption,
		string $shortOption = null,
		string $example = null
	):void {
		$this->requiredParameterList []= new Parameter(
			$requireValue,
			$longOption,
			$shortOption,
			$example
		);
	}

	public function getArgumentValueList(
		ArgumentList $arguments
	):ArgumentValueList {
		$namedParameterIndex = 0;
		/** @var NamedParameter[] */
		$namedParameterList = array_merge(
			$this->requiredNamedParameterList,
			$this->optionalNamedParameterList
		);

		$parameterIndex = 0;
		/** @var Parameter[] $parameterList */
		$parameterList = array_merge(
			$this->requiredParameterList,
			$this->optionalParameterList
		);

		$argumentValueList = new ArgumentValueList();

		foreach($arguments as $argument) {
			if($argument instanceof NamedArgument) {
				/** @var NamedParameter $parameter */
				$parameter = $namedParameterList[
					$namedParameterIndex
				];

				$argumentValueList->set(
					$parameter->getOptionName(),
					$argument->getValue()
				);
				$namedParameterIndex++;
			}
			elseif($argument instanceof Argument) {
				/** @var Parameter $parameter */
				$parameter = $parameterList[
					$parameterIndex
				];

				$argumentValueList->set(
					$parameter->getLongOption(),
					$argument->getValue()
				);

				$parameterIndex++;
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