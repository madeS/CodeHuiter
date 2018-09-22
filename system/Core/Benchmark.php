<?php
namespace CodeHuiter\Core;

class Benchmark
{
    const SERVICE_KEY = 'benchmark';
    const APP_START = 'AppStart';
    
    /**
     * List of all benchmark markers
     *
     * @var	array
     */
    protected $times = [];

    protected $markers = [];

    protected $results = [];

    /** @var \Composer\Autoload\ClassLoader $autoloader */
    protected $autoloader;

    protected $loadedClasses = [];

    public function __construct() {
        $this->mark(self::APP_START);
    }

    public function setAutoloader(\Composer\Autoload\ClassLoader $autoloader)
    {
        $this->autoloader = $autoloader;
        //$this->calculateLoadedClasses();
    }

    protected function calculateLoadedClasses()
    {
        spl_autoload_register(array($this, 'loadClass'), true, true);
    }

    public function loadClass($class)
    {
        if(!isset($this->loadedClasses[$class])) {
            $this->loadedClasses[$class] = true;
        }
    }

    /**
     * Set a benchmark marker
     *
     * @param string $name Marker name
     * @return void
     */
    public function mark(string $name) {
        $this->times[$name] = microtime(true);
        $this->markers[] = $name;
    }
    
   /**
     * Elapsed time
     * Calculates the time difference between two marked points.
     *
     * @param string $point1
     * @param string $point2
     * @return float
     */
    public function elapsedTime($point1 = '', $point2 = '') {
        if ($point2 === '') {
            $point2 = $point1;
            $point1 = self::APP_START;
        }
        if (!isset($this->times[$point2])) {
            $this->mark($point2);
        }
        return $this->times[$point2] - $this->times[$point1];
    }
    
   /**
     * Elapsed time
     * Calculates the time difference between two marked points.
     *
     * @param string $point1
     * @param string $point2
     * @return string
     */
    public function elapsedString($point1 = '', $point2 = '')
    {
        return number_format($this->elapsedTime($point1, $point2), 4);
    }
    
    /**
     * Memory Usage
     *
     * @return int
     */
    public function memory()
    {
        return memory_get_usage(false);
    }

    /**
     * Memory Usage as string
     * 
     * @return string
     */
    public function memoryString()
    {
        return round(memory_get_usage() / 1024 / 1024, 2).' MB';
    }

    protected function generateTimeData()
    {
        $this->results = [];
        $totalElapsedTime = 0;
        foreach ($this->markers as $key => $markerName) {
            if ($key === 0) continue;
            $previousMarkerName = $this->markers[$key-1];
            $elapsedTime = $this->times[$markerName] - $this->times[$previousMarkerName];
            $totalElapsedTime += $elapsedTime;
            $this->results[] = [
                'marker' => "$previousMarkerName - {$markerName}",
                'time' => $elapsedTime,
                'format_time' => number_format($elapsedTime,8),
                'elapsed_time' => number_format($totalElapsedTime,8),
                'percent' => '',
            ];
        }
        foreach ($this->results as $key => $result) {
            $this->results[$key]['percent'] = number_format(
                ($result['time'] * 100 / $totalElapsedTime),
                3, '.', ''
            );
        }
    }

    protected function getLoadedClasses(){
        return $this->loadedClasses;
    }

    public function totalTimeTable(){
        $this->mark('BenchmarkEND');
        $this->generateTimeData();

        $ret = '<div style="">';
        $ret .= '    <div style=";">';
        $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">MARK</div>';
        $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">ELAPSED STEP</div>';
        $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">ELAPSED TOTAL</div>';
        $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">PERCENT</div>';
        $ret .= '    </div>';
        foreach ($this->results as $result) {
            $ret .= '    <div style=";">';
            $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">'.$result['marker'].'</div>';
            $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">'.$result['format_time'].'</div>';
            $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">'.$result['elapsed_time'].'</div>';
            $ret .= '        <div style=" width: 24%; display:inline-block; vertical-align:top;">'.$result['percent'].'</div>';
            $ret .= '    </div>';
        }
        $ret .= '</div>';

        return $ret;
    }

    public function totalLoadedTable()
    {
        $classes = $this->getLoadedClasses();

        $ret = '<div style="">';
        $ret .= '    <div style=";">';
        $ret .= '        <div style=" width: 66%; display:inline-block; vertical-align:top;">LOADED CLASSES</div>';
        $ret .= '    </div>';
        foreach ($classes as $class => $result) {
            $ret .= '    <div style=";">';
            $ret .= '        <div style=" width: 66%; display:inline-block; vertical-align:top;">'. $class .'</div>';
            $ret .= '    </div>';
        }
        $ret .= '</div>';

        return $ret;
    }
}
