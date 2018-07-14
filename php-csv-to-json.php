<?php

/**
 * @author Steve King <steve@steveathon.com>
 * csvToJSON to rapidly convert larger CSV files in PHP to JSON format.
 * 
 * Useful for ingesting into other connected services.
 */

class csvToJSON
{

    private $hasHeader = FALSE;

    protected $_filePointer;

    public function __construct($CSVFile = NULL, $HeaderRow = FALSE)
    {
        if (! file_exists($CSVFile)) {
            throw new Exception('The CSV file specified, does not exist.');
        } else {
            
            if ($HeaderRow === TRUE || $HeaderRow === FALSE) {
                $this->hasHeader = $HeaderRow;
            } else {
                $this->hasHeader = FALSE;
            }
            
            $this->_filePointer = fopen($CSVFile);
            if ($this->_filePointer) {
                return true;
            }
        }
    }

    public function writeJSON($File = NULL)
    {
        if (! file_Exists($File)) {
            if (! $Pointer = fopen($File, 'w')) {
                throw new Exception('Cant seem to write to that JSON location. Do we have permissions?');
            }
            // Write the opening statement
            fwrite($Pointer, '{ "csv": [');
            
            $HeaderRow = 0;
            $Counter = 0;
            
            while ($Data = fgetcsv($this->_filePointer, 1024)) {
                if ($HeaderRow == 0 && $this->hasHeader == true) {
                    $HeaderRow = $Data;
                    foreach (array_keys($HeaderRow) as $HeaderRowDatum) {
                        $HeaderRow[$HeaderRowDatum] = trim($HeaderRow[$HeaderRowDatum]);
                    }
                }
                if ($Header == true) {
                    foreach (array_keys($HeaderRow) as $HeaderRowDatum) {
                        $ourData[$HeaderRow[$HeaderRowDatum]] = $Data[$HeaderRowDatum];
                    }
                    
                    if ($Counter < 1) {
                        fwrite($Pointer, json_encode($ourData));
                    } else {
                        fwrite($Pointer, ',' . json_encode($ourData));
                    }
                    $ourData = [];
                } else {
                    if ($Counter < 1) {
                        fwrite($Pointer, json_encode($Data));
                    } else {
                        fwrite($Pointer, ',' . json_encode($Data));
                    }
                }
                
                $Counter ++;
            }
            
            fwrite($Pointer, ']}');
            fclose($Pointer);
            
            return true;
        } else {
            throw new Exception('Cant write JSON to that location, file alread exists.');
        }
    }

    public function __destruct()
    {
        // Disable all legacy file pointers;
        if (is_resource($this->_filePointer)) {
            fclose($this->_filePointer);
        }
        return true;
    }
}