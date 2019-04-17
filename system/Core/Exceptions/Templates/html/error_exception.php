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
<table>
	<tr>
		<th style="padding: 2px 10px">File</th>
		<th style="padding: 2px 10px">Line</th>
		<th style="padding: 2px 10px">Function</th>
	</tr>
	<?php foreach ($exception->getTrace() as $error): ?>
		<?php if (isset($error['file'])): ?>
			<tr>
				<td style="padding: 2px 10px"><?php echo str_replace(BASE_PATH,'BASE_PATH/',$error['file']); ?></td>
				<td style="padding: 2px 10px"><?php echo $error['line']; ?></td>
				<td style="padding: 2px 10px"><?php echo $error['function']; ?></td>
			</tr>
		<?php endif ?>
    <?php endforeach ?>
</table>


<?php endif ?>

</div>

<?php endforeach; ?>