<?php

namespace CodeHuiter\Services;

class Processes
{
    protected $processes = [];

    public function __construct()
    {
    }

    /**
     * @return array array of pid
     */
    public function getActive()
    {
        foreach ($this->processes as $pid => $processActive) {
            if (!$this->isActive($pid)) {
                unset($this->processes[$pid]);
            }
        }

        return array_values($this->processes);
    }

    /**
     * @param string $pid
     * @return bool is Active
     */
    public function isActive($pid)
    {
        try {
            $result = shell_exec(sprintf("ps %d", $pid));
            if( count(preg_split("/\n/", $result)) > 2){
                return true;
            }
        } catch (\Exception $e) { }

        return false;
    }

    /**
     * @param string $command Command to Run
     * @param string $outputFile Output Data file
     * @param null $uniqueName Unique pid or null to generate automatic
     * @return string pid
     */
    public function runAsync($command, $outputFile, $uniqueName = null)
    {
        $pidFile = $uniqueName;
        if (!$uniqueName) {
            $pidFile = md5(rand(0,10000000));
        }

        exec(
            sprintf(
                "%s > %s 2>&1 & echo $! >> %s", // " > /dev/null 2>/dev/null &" // output and stderror
                $command,
                $outputFile,
                $pidFile
            )
        );

        $this->processes[$pidFile] = true;
        return $pidFile;
    }

    /**
     * @param string $command Command to Run
     * @return array output Data
     */
    public function run($command)
    {
        $output = [];
        exec($command, $output);
        return $output;
    }

}
