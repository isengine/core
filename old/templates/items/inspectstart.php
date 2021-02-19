<?php defined('isENGINE') or die;

global $loadingLog;
global $loadingStart;
global $loadingMemory;
global $query;

if (in('options', 'inspect')) {
	
	$loadingLog = '';
	$loadingMemory = memory_get_peak_usage();
	
	if (isset($query)) {
		
		$loadingLog .= 'this page was loaded with valid query!\n';
		$loadingLog .= 'query name is ' . (!empty($query -> name) ? '\"' . htmlentities($query -> name) . '\"' : 'none') . '\n';
		$loadingLog .= 'query status is ' . (!empty($query -> status) ? '\"' . htmlentities($query -> status) . '\"' : 'none') . '\n';
		$loadingLog .= 'query method is ' . (!empty($query -> method) ? '\"' . htmlentities($query -> method) . '\"' : 'none') . '\n';
		$loadingLog .= 'query data is ' . (isset($query -> data) && (is_array($query -> data) || is_object($query -> data)) ? '\"' . htmlentities(objectToString($query -> data, ', ', true)) . '\"' : 'none') . '\n';
		$loadingLog .= 'query errors is ' . (isset($query -> errors) && (is_array($query -> errors) || is_object($query -> errors)) ? '\"' . htmlentities(objectToString($query -> errors, ', ', true)) . '\"' : 'none') . '\n\n';
		
	}
	
}

?>

<script>
	// временные метки, когда началось выполнение скриптов (в js и unix формате)
	var loadingStart = performance.now();
	<?php $loadingStart = microtime(true); ?>
</script>
