phped-gettersAndSetters
=======================

## Introduction
This script is meant for the NuSphere PhpED editor. The lack of functionality for creating getter and setters was really annoying. With this script enabled in you PhpED working environment you can create getter and setters with a simple mouseclick.

## Installation
1. Place the file 'generateGettersAndSetters.php' on your filesystem and note the full path to the file
2. Retrieve the full path to the php.exe (This can the executable from PhpED, but it can also be the exectuable of your local webserver)
3. Open PhpED and go to tools -> settings -> Section Tools -> Integration (Last item in the list)
4. Click on Add Menu and call it 'Generators'
5. Click on Sub Menu and call it 'Getters / Setters' and double click on the just created menu item
6. Fill in the next items
    * Execute with: Shell
    * Command Line: "path to php.exe" -f "path to php file" "@FName@" --getters=1 --setters=1
    * Check the following items:
        * 'Show this command in Workspace popup' and the subitem 'for files'
        * 'Show this command in explorer popup' and the subitem 'for files'
        * 'Return results to editor'
        * 'Redirect Error Stream to log window'
    
    **Note**: Do not check refresh editor. This will break the insert!
7. Repeat steps 5 and 6 for multiple options. for example:
    - Getters Only : "path to php.exe" "path to php file" --getters=1 --setters=0 "@FName@"
    - Setters Only : "path to php.exe" "path to php file" --getters=0 --setters=1 "@FName@"


## Command line usage
This script can also be used in the command line
```bash
Syntaxis: generateGettersAndSetters.php [--tabchar tab] [--docblock boolean] 
                                        [--getters boolean] [--setters boolean] 
                                        [--ignore property] filename

Options:
    --tabchar tab         Which characters should be used as tab (Default: 4x space)
    --docblock boolean    Add docblocks above the get/set methods
    --getters boolean     Create getters
    --setters boolean     Create setters
    --ignore property     The property that shouldn't get a getter and a setter
```

## Limitations
* The script only support 1 class per file at the moment.

## Todo
There are still things that should improved.

* Support for multiple classes in the same file
