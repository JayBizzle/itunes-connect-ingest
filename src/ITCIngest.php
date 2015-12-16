<?php

namespace Jaybizzle;

class ITCIngest
{
    public $username;
    public $password;
    public $vndnumber;
    public $filename;
    public $date;
    public $data = [];

    public function __construct($username, $password, $vndnumber)
    {
        $this->username = $username;
        $this->password = $password;
        $this->vndnumber = $vndnumber;
    }

    /**
     * Initiate ITC Ingest.
     * 
     * @param string $date DD/MM/YYYY
     *
     * @return array
     */
    public function getData($date)
    {
        $this->filename = "{$date}-{$this->vndnumber}";
        $this->date = $date;
        if ($this->sendRequest()) {
            return $this->processCsv();
        }
    }

    /**
     * Build the post paramateres.
     * 
     * @return string
     */
    public function buildParams()
    {
        return http_build_query([
            'USERNAME'     => $this->username,
            'PASSWORD'     => $this->password,
            'VNDNUMBER'    => $this->vndnumber,
            'TYPEOFREPORT' => 'Sales',
            'DATETYPE'     => 'Daily',
            'REPORTTYPE'   => 'Summary',
            'REPORTDATE'   => $this->date,
        ]);
    }

    /**
     * Execute the post request.
     * 
     * @return mixed
     */
    public function sendRequest()
    {
        $ch = curl_init();
        $fp = fopen("$this->filename.gz", 'w');

        // set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://reportingitc.apple.com/autoingestion.tft');
        curl_setopt($ch, CURLOPT_POST, 7);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildParams());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        // execute post request
        return curl_exec($ch);
    }

    /**
     * Process the CSV data.
     * 
     * @return array
     */
    public function processCsv()
    {
        if (filesize("$this->filename.gz")) {
            $this->decode();

            if (($handle = fopen("$this->filename", 'r')) !== false) {
                // get first line to use as array keys
                $columns = fgetcsv($handle, 0, "\t");

                $columns = $this->keysToCamel($columns);

                while (($data = fgetcsv($handle, 0, "\t")) !== false) {
                    $this->data[$this->date][] = array_combine($columns, $data);
                }

                fclose($handle);
            } else {
                echo "Could not open $this->filename for reading".PHP_EOL;
            }

            $this->cleanup();

            return $this->data;
        } else {
            echo 'File is of size 0'.PHP_EOL;
            $this->cleanup();
        }
    }

    /**
     * Extract the .gz file.
     * 
     * @return void
     */
    public function decode()
    {
        file_put_contents($this->filename, gzdecode(file_get_contents("$this->filename.gz")));
    }

    /**
     * Convert array keys to camel case.
     * 
     * @param array $array
     *
     * @return array
     */
    public function keysToCamel($array)
    {
        array_walk($array, function (&$value) {
            $value = lcfirst(str_replace(' ', '', ucwords(strtolower($value))));
        });

        return $array;
    }

    /**
     * Cleanup temporay files.
     * 
     * @return void
     */
    public function cleanup()
    {
        if(file_exists("$this->filename.gz")) {
            unlink("$this->filename.gz");
        }
        
        if(file_exists("$this->filename")) {
            unlink("$this->filename");
        }
    }
}
