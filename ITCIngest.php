<?php

class ITCIngest
{
    public $username;
    public $password;
    public $vndnumber;
    public $filename;

    public function __construct($username, $password, $vndnumber)
    {
        echo 'woop';
        $this->username = $username;
        $this->password = $password;
        $this->vndnumber = $vndnumber;
    }

    public function getData($date)
    {
        $this->filename = "{$date}-{$this->vndnumber}";
        if ($this->sendRequest()) {
            $this->processCsv();
        }
    }

    public function buildParams()
    {
        return http_build_query([
            'USERNAME'     => $this->username,
            'PASSWORD'     => $this->password,
            'VNDNUMBER'    => $this->vndnumber,
            'TYPEOFREPORT' => 'Sales',
            'DATETYPE'     => 'Daily',
            'REPORTTYPE'   => 'Summary',
            'REPORTDATE'   => $date,
        ]);
    }

    public function sendRequest()
    {
        $ch = curl_init();
        $fp = fopen("$filename.gz", 'w');

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'https://reportingitc.apple.com/autoingestion.tft');
        curl_setopt($ch, CURLOPT_POST, 7);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildParams());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        //execute post
        return curl_exec($ch);
    }

    public function processCsv()
    {
        if (filesize("$this->filename.gz")) {
            if (function_exists('gzdecode')) {
                file_put_contents($this->filename, gzdecode(file_get_contents("$this->filename.gz")));
            } else {
                exec("gunzip $this->filename.gz");
            }

            if (($handle = fopen("$this->filename", 'r')) !== false) {
                // get first line to use as array keys
                $columns = fgetcsv($handle, 0, "\t");

                while (($data = fgetcsv($handle, 0, "\t")) !== false) {
                    var_dump(array_combine($columns, $data));
                }

                fclose($handle);
            } else {
                echo "Could not open $this->filename for reading".PHP_EOL;
            }

            unlink("$this->filename");
        } else {
            echo 'File is of size 0'.PHP_EOL;
            unlink("$this->filename.gz");
        }
    }
}

$itc = new ITCIngest('itunesconnect@emailaddress.com', 'itun3sp455word', 'nndnumber');
$itc->getData('20151207');
