<?php

namespace CodeHuiter\Service\ByDefault;

use Exception;

class Processes implements \CodeHuiter\Service\Processes
{
    protected $processes = [];

    public function __construct()
    {
    }

    /**
     * @return string[] array of pid
     */
    public function getActive(): array
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
    public function isActive(string $pid): bool
    {
        try {
            $result = shell_exec(sprintf('ps %d', $pid));
            if( count(explode("\n", $result)) > 2){
                return true;
            }
        } catch (Exception $e) { }

        return false;
    }

    /**
     * @param string $command Command to Run
     * @param string $outputFile Output Data file
     * @param string|null $uniqueName Unique pid or null to generate automatic
     * @return string pid
     * @throws Exception
     */
    public function runAsync(string $command, string $outputFile, ?string $uniqueName = null): string
    {
        $pidFile = $uniqueName;
        if (!$uniqueName) {
            $pidFile = md5(random_int(0,10000000));
        }

        exec(
            sprintf(
                '%s > %s 2>&1 & echo $! >> %s', // " > /dev/null 2>/dev/null &" // output and stderror
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
    public function run(string $command): ?array
    {
        $output = [];
        exec($command, $output);
        return $output;
    }
}
