<?php

/**
* This class can create Getters / Setters for a given filename
*
* @author J. Dolieslager
* @package phped-create-gettersAndSetters
*
* Syntaxis: generateGettersAndSetters.php [--tabchar tab] [--docblock boolean]
*                                         [--getters boolean] [--setters boolean]
*                                         [--ignore property] filename
* Options:
*    --tabchar tab         Which characters should be used as tab (Default: 4x space)
*    --docblock boolean    Add docblocks above the get/set methods
*    --getters boolean     Create getters
*    --setters boolean     Create setters
*    --ignore property     The property that shouldn't get a getter and a setter
*
*/
class CreateGettersAndSetters
{
    /** @var boolean */
    protected $createSetters = true;

    /** @var boolean */
    protected $createGetters = true;

    /** @var boolean */
    protected $createDocblock = true;

    /** @var string */
    protected $tabchar       = '    ';

    /** @var string */
    protected $file;

    /** @var array */
    protected $ignoreProperties = array();

    /** @var array */
    protected $validBooleanParameters = array(
        false,
        true,
        0,
        1,
        '0',
        '1',
        'false',
        'true',
    );

    /**
    * Parameter will be used as --key
    *
    * @var array
    */
    protected $parameters = array(
        'tabchar' => array(
            'required'  => false,
            'setter'    => 'setTabchar',
            'short_description' => 'tab',
            'long_description' => 'Which characters should be used as tab (Default: 4x space)',
        ),
        'docblock' => array(
            'required'  => false,
            'setter'    => 'setCreateDocblock',
            'short_description' => 'boolean',
            'long_description' => 'Add docblocks above the get/set methods',
        ),
        'getters' => array(
            'required'  => false,
            'setter'    => 'setCreateGetters',
            'short_description' => 'boolean',
            'long_description' => 'Create getters',
        ),
        'setters' => array(
            'required'  => false,
            'setter'    => 'setCreateSetters',
            'short_description' => 'boolean',
            'long_description' => 'Create setters',
        ),
        'ignore' => array(
            'required'  => false,
            'setter'    => 'addIgnoreProperty',
            'short_description' => 'property',
            'long_description' => 'The property that shouldn\'t get a getter and a setter',
        ),
    );

    /**
    * Parse the argument given by the request and validate the arguments
    */
    protected function parseArguments()
    {
        if ($_SERVER['argc'] < 2) {
            throw new \UnexpectedValueException('filename should be defined!');
        }

        $arguments = $_SERVER['argv'];
        array_shift($arguments);

        $filename = array_pop($arguments);

        if (is_file($filename) === false) {
            throw new \UnexpectedValueException("File {$filename} does not exists. Provide a valid file");
        }

        $this->setFile($filename);

        foreach($arguments as $argument) {
            $argumentsAllowed = array_keys($this->parameters);
            $argumentsAllowed = implode('|', $argumentsAllowed);

            $regex = "/--({$argumentsAllowed})=(.*)/";

            if (!preg_match($regex, $argument, $matches)) {
                throw new \UnexpectedValueException("Argument `{$argument}` does not match pattern: {$regex}");
            }

            $setter = $this->parameters[$matches[1]]['setter'];
            $this->{$setter}($matches[2], $matches[1]);
        }
    }

    /**
    * Set what the tabChar should be
    * Default: 4x spaces
    *
    * @param string|integer $tabchar The tabchar you want
    * @param string $parameterKey The argumentname
    *
    * @return CreateGettersAndSetters
    */
    public function setTabchar($tabchar, $parameterKey)
    {
        $this->tabchar = $tabchar;
        return $this;
    }

    /**
    * What the tabchar is
    *
    * @return string
    */
    public function getTabchar()
    {
        return $this->tabchar;
    }

    /**
    * Set if we need to create docblocks above the methods
    *
    * @param string|boolean|integer $createDocblock
    * @param string $parameterKey the argumentname
    *
    * @return CreateGettersAndSetters
    */
    public function setCreateDocblock($createDocblock, $parameterKey = null)
    {
        $this->createDocblock = $this->convertParameterToBoolean($createDocblock, $parameterKey);
        return $this;
    }

    /**
    * If we should create docblocks above the methods
    *
    * @return boolean
    */
    public function getCreateDocblock()
    {
        return $this->createDocblock;
    }

    /**
    * If we should create getters or not
    *
    * @param string|boolean|integer $createGetters
    * @param string $parameterKey the argumentname
    *
    * @return CreateGettersAndSetters
    */
    public function setCreateGetters($createGetters, $parameterKey = null)
    {
        $this->createGetters = $this->convertParameterToBoolean($createGetters, $parameterKey);
        return $this;
    }

    /**
    * Should we create getters
    *
    * @return boolean
    */
    public function getCreateGetters()
    {
        return $this->createGetters;
    }

    /**
    * If we should create setters or not
    *
    * @param string|integer|boolean $createSetters
    * @param string $parameterKey The argumentname
    *
    * @return CreateGettersAndSetters
    */
    public function setCreateSetters($createSetters, $parameterKey = null)
    {
        $this->createSetters = $this->convertParameterToBoolean($createSetters, $parameterKey);
        return $this;
    }

    /**
    * If we should generate setters
    *
    * @return boolean
    */
    public function getCreateSetters()
    {
        return $this->createSetters;
    }

    /**
    * Set a list of ignore properties
    * When string given it will internal converted to an array
    *
    * @param string|array $ignoreProperties The property(ies) you want to ignore
    * @param string $parameterKey The argumentname
    *
    * @return CreateGettersAndSetters
    */
    public function setIgnoreProperties($ignoreProperties, $parameterKey = null)
    {
        $this->ignoreProperties = is_array($ignoreProperties) ? $ignoreProperties : array($ignoreProperties);
        return $this;
    }

    /**
    * Add an entry to the ignore property list
    *
    * @param string $ignoreProperty the name of the property
    * @param string $parameterKey the argumentname
    *
    * @return CreateGettersAndSetters
    */
    public function addIgnoreProperty($ignoreProperty, $parameterKey = null)
    {
        $this->ignoreProperties[] = $ignoreProperty;
        return $this;
    }

    /**
    * Get the properties that we should ignore
    *
    * @return array
    */
    public function getIgnoreProperties()
    {
        return $this->ignoreProperties;
    }

    /**
    * Set the filename that we're going to parsed
    *
    * @param string $file
    * @return CreateGettersAndSetters
    */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
    * @return string The filename that will be parsed
    */
    public function getFile()
    {
        return $this->file;
    }

    /**
    * Check if the boolean argument is a valid 'boolean'
    *
    * @param string $value The argument value
    * @return boolean TRUE on success | FALSE on failure
    * @throws \UnexpectedValueException
    */
    protected function isValidBooleanParameter($value)
    {
        return in_array($value, $this->validBooleanParameters, true);
    }

    /**
    * Convert the parameter input to a PHP boolean
    *
    * @param string $parameter the value of the argument
    * @param string $parameterKey The argumentname
    *
    * @return boolean
    * @throws \UnexpectedValueException
    */
    protected function convertParameterToBoolean($parameter, $parameterKey)
    {
        if ($this->isValidBooleanParameter($parameter) === false) {
            throw new \InvalidArgumentException("Argument --{$parameterKey} is not a boolean!".
                "Use (0, 1, false, true)");
        }

        if ($parameter === false || $parameter === 'false' || $parameter === 0 || $parameter === '0') {
            return false;
        } else if ($parameter === true || $parameter === 'true' || $parameter === 1 || $parameter === '1') {
            return true;
        }

        throw new \UnexpectedValueException(
            "Boolean parameter ".var_export($parameter, true)." has not be implemented by the programmer."
        );
    }

    /**
    * Generates the new file
    *
    * @return string the new generated file
    * @throws \UnexpectedValueException
    */
    public function generateNewFile()
    {
        $this->parseArguments();

        $file = $this->getFile();

        if ($file === null || is_file($file) === false) {
            throw new \UnexpectedValueException("File `{$file}` does not exists or is not set!");
        }

        $content = file_get_contents($file);

        $className          = $this->fetchClassName($content);
        $properties         = $this->fetchClassProperties($content);
        $functions          = $this->fetchClassFunctions($content);
        $ignoreProperties   = $this->getIgnoreProperties();

        // Strip off the ignore properties
        $properties         = array_diff($properties, $ignoreProperties);

        $functions  = $this->fetchClassFunctions($content);
        $injection  = '';

        foreach($properties as $property) {
            $injection .= $this->createInjectionForProperty($property, $functions, $className);
        }

        return $this->insertInjection($content, $injection);
    }

    /**
    * All the logic for one property
    *
    * @param string $property The property you want to add
    * @param array $functions A list of used functions in the class
    * @param string $className The class
    *
    * @return string All the methods created
    */
    protected function createInjectionForProperty($property, $functions, $className)
    {
        $setFunction = "set".ucfirst($property);
        $getFunction = "get".ucfirst($property);

        $returnValue = '';

        if ($this->getCreateSetters() && $this->isFunctionDefined($setFunction, $functions) === false) {
            $returnValue .= $this->createSetter($property, $setFunction, $className);
        }

        if ($this->getCreateGetters() && $this->isFunctionDefined($getFunction, $functions) === false) {
            $returnValue .= $this->createGetter($property, $getFunction, $className);
        }

        return $returnValue;
    }

    /**
    * Creates the setter method for a given property
    *
    * @param string $property the name of the property
    * @param string $functionName the name of the function
    * @param string $className the classname
    *
    * @return string The method
    */
    protected function createSetter($property, $functionName, $className)
    {
        $injection  = '';
        $tab        = $this->getTabchar();
        $variable   = '$'.$property;

        if ($this->getCreateDocblock()) {
            $injection .= "{$tab}/**\n".
                          "{$tab} *\n".
                          "{$tab} * @param mixed {$variable}\n".
                          "{$tab} *\n".
                          "{$tab} * @return {$className} \n".
                          "{$tab} */\n";
        }

        $injection .= "{$tab}public function {$functionName}({$variable})\n".
                      "{$tab}{\n{$tab}{$tab }\$this->{$property} = {$variable};\n".
                      "{$tab}{$tab}return \$this;\n{$tab}}\n\n";

        return $injection;
    }

    /**
    * Create the get method
    *
    * @param string $property The propertyname
    * @param string $functionName The name for the function
    * @param string $className The name of the class
    *
    * @return string The method
    */
    protected function createGetter($property, $functionName, $className)
    {
        $injection  = '';
        $tab        = $this->getTabchar();

        if ($this->getCreateDocblock()) {
            $injection .= "{$tab}/**\n".
                          "{$tab} * @return mixed \n".
                          "{$tab} */\n";
        }

        $injection .= "{$tab}public function {$functionName}()\n".
                      "{$tab}{\n{$tab}{$tab}return \$this->{$property};\n{$tab}}\n\n";

        return $injection;
    }

    /**
    * fetch the classname from the string
    *
    * @param string $string The whole class
    *
    * @return sting the classname
    * @throws \UnexpectedValueException
    */
    protected function fetchClassName($string)
    {
        if (!preg_match('/(.*)class(.+?){(.*)}(.*)/si', $string, $class)) {
            throw new \UnexpectedValueException('Cannot find a class in the file!');
        }

        $className = trim($class[2]);
    }

    /**
    * Get all the class properties of a class
    *
    * @param string $string The whole class as string
    *
    * @return array A list of property names
    * @throws \UnexpectedValueException
    */
    protected function fetchClassProperties($string)
    {
        if(!preg_match_all(
            '/(protected|private|public|var)\s+\$([^\s]+);/is',
            $string,
            $properties,
            PREG_SET_ORDER
        )) {
            throw new \UnexpectedValueException("There are no properties defined in class {$className}");
        }

        foreach($properties as $idx => $property) {
            $properties[$idx] = $property[2];
        }

        return $properties;
    }

    /**
    * Grep the functions defined in the class
    *
    * @param strign $string The whole class as string
    *
    * @return array A list of functions
    */
    protected function fetchClassFunctions($string)
    {
        preg_match_all('/function\s+([a-z_]+)\s{0,}\(/i', $string, $functions, PREG_SET_ORDER);

        foreach($functions as $idx => $function) {
            $functions[$idx] = $function[1];
        }

        return $functions;
    }

    /**
    * Insert the getters and setters in the class
    *
    * @param string $originalFileContents the file where it should be injected into
    * @param string $injectionCode The code that should be injected
    *
    * @return string the origin content with the injected code
    */
    protected function insertInjection($originalFileContents, $injectionCode)
    {
        if (!preg_match('/(.*)class(.+?){(.*)}(.*)/si', $originalFileContents, $class)) {
            throw new \UnexpectedValueException('Cannot find a class in the file!');
        }

        return "{$class[1]}class{$class[2]}{{$class[3]}\n{$injectionCode}}{$class[4]}";
    }

    /**
    * Check of a function is already in the function list
    *
    * @param string $function the needler
    * @param array $functions The haystack
    *
    * @return boolean TRUE on defined | FALSE on not defined
    */
    protected function isFunctionDefined($function, $functions)
    {
        return in_array($function, $functions);
    }

    /**
    * Print Error / Help on the screen
    *
    * @param string $message The error message
    */
    public function printError($message)
    {
        echo $message.PHP_EOL.PHP_EOL.
            "Syntaxis: ".basename(__FILE__);

        foreach($this->parameters as $argument => $settings) {
            echo " [--{$argument} {$settings['short_description']}]";
        }

        echo ' filename'.PHP_EOL.PHP_EOL.
             'Options:'.PHP_EOL;

        $max = 0;
        foreach($this->parameters as $argument => $settings) {
            $len = strlen("    --{$argument} {$settings['short_description']}");
            if ($len > $max) {
                $max = $len;
            }
        }

        $max += 4;

        foreach($this->parameters as $argument => $settings) {
            $argument = "    --{$argument} {$settings['short_description']}";

            $len = strlen($argument);

            echo $argument.str_repeat(' ', $max - $len).$settings['long_description'].PHP_EOL;
        }

        echo PHP_EOL;


    }
}

if (php_sapi_name() !== 'cli') {
    throw new \UnexpectedValueException('This script should be run under CLI');
}

try {
    $program = new CreateGettersAndSetters();
    echo $program->generateNewFile();
} Catch (Exception $e) {
    $program->printError($e->getMessage());
}

