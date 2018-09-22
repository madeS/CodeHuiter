<?php
/** @var \Exception[] $exceptions */
/** @var bool $show_debug_backtrace */
?>

<?php foreach ($exceptions as $exception): ?>
<?php
    $message = $exception->getMessage();
    $fileName = $exception->getFile();
    $fileLine = $exception->getLine();
?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>
    <?php if ($exception instanceof \CodeHuiter\Exceptions\PhpErrorException):?>
        A PHP Error [<?php echo $exception->getSeverity(); ?>]
        <?php
            $fileName = $exception->getErrorFile();
            $fileLine = $exception->getErrorLine();
        ?>
    <?php else:?>
        An uncaught Exception
    <?php endif;?>
    was encountered
</h4>

<p>Type: <?php echo get_class($exception); ?></p>
<p>Message: <?php echo $message; ?></p>
<p>Filename: <?php echo $fileName; ?></p>
<p>Line Number: <?php echo $fileLine; ?></p>

<?php if ($show_debug_backtrace): ?>

	<p>Backtrace:</p>
	<?php foreach ($exception->getTrace() as $error): ?>

		<?php if (isset($error['file'])): ?>

			<p style="margin-left:10px">
			File: <?php echo str_replace(BASE_PATH,'BASE_PATH/',$error['file']); ?><br />
			Line: <?php echo $error['line']; ?><br />
			Function: <?php echo $error['function']; ?>
			</p>
		<?php endif ?>

	<?php endforeach ?>

<?php endif ?>

</div>

<?php endforeach; ?>