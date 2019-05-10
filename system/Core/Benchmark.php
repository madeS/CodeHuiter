<?php
namespace CodeHuiter\Core;

use CodeHuiter\Core\Exceptions\ExceptionProcessor;

class Benchmark
{
    const APP_START = 'AppStart';

    private const CLASSES_CACHE_FILE = 'autoload/loadedClasses.php';
    private const CLASSES_CACHE_FILE_CLEAR_LOG = 'autoload/clearLog.php';
    public const BENCH_MODE_SCRIPT_START = 1;
    public const BENCH_MODE_APP_INIT = 2;

    public const GET_DEBUG_BENCH_ENABLE = 'debug_bench';

    private const CLASS_PLACE = [
        'App' => 'app',
        'CodeHuiter' => 'system',
    ];

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

    protected $loadedClasses = null;

    public $benchMode = 0;


    public function __construct() {
        $options = getopt("m::");
        $this->benchMode = $options['m'] ?? 0;
        $this->mark(self::APP_START);
    }

    public function setAutoloader(\Composer\Autoload\ClassLoader $autoloader)
    {
        $this->autoloader = $autoloader;
        spl_autoload_register(array($this, 'loadClass'), true, true);
    }

    public function loadClass($class)
    {
        if ($this->loadedClasses === null) {
            if (file_exists(CACHE_PATH . self::CLASSES_CACHE_FILE)) {
                $this->loadedClasses = include CACHE_PATH . self::CLASSES_CACHE_FILE;
            }
            if (!is_array($this->loadedClasses)) {
                $this->loadedClasses = [];
            }
        }
        if (isset($this->loadedClasses[$class])) {
            include BASE_PATH . $this->loadedClasses[$class];
            if (class_exists($class) || interface_exists($class)) {
                return true;
            }
            if (file_exists(CACHE_PATH . self::CLASSES_CACHE_FILE)) {
                // Invalid cache file
                unlink(CACHE_PATH . self::CLASSES_CACHE_FILE);
                file_put_contents(
                    CACHE_PATH . self::CLASSES_CACHE_FILE_CLEAR_LOG,
                    "Invalid cache for class [$class] got[{$this->loadedClasses[$class]}]. Log cleared" . ";\n",
                    LOCK_EX
                );
            }
        }
        $explodedClass = explode('\\', $class);
        foreach (self::CLASS_PLACE as $classStart => $classRoot) {
            if ($explodedClass[0] === $classStart) {
                $explodedClass[0] = $classRoot;
            }
        }
        $file = implode(DIRECTORY_SEPARATOR, $explodedClass) . '.php';

        $success = false;
        if (file_exists(BASE_PATH . $file)) {
            include BASE_PATH . $file;
            if (class_exists($class) || interface_exists($class)) {
                $this->loadedClasses[$class] = $file;
                $success = true;
            } else {
                $this->loadedClasses['INVALID_' . $class] = $file;
                $success = false;
            }
        } else {
            $this->loadedClasses['UNKNOWN_' . $class] = $file;
            $success = false;
        }

        file_put_contents(
            CACHE_PATH . self::CLASSES_CACHE_FILE,
            "<?php\n return " . var_export($this->loadedClasses, true) . ";\n",
            LOCK_EX
        );
        return $success;
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
        $ret .= '        <div style=" width: 66%; display:inline-block; vertical-align:top;">LOADED CLASSES ('.count($classes).')</div>';
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
