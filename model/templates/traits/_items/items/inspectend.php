<?php defined('isENGINE') or die;

global $loadingPage;
global $loadingLog;
global $loadingStart;
global $loadingMemory;

?>

<script>
	window.onload = function() {
		// временные метки, когда окончилось выполнение скриптов (в js и unix формате)
		var loadingPage = performance.now();
		
		<?php
			$loadingPage = microtime(true);
			
			// общее время загрузки в PHP в секунах: round(loadingPage - $_SERVER['REQUEST_TIME_FLOAT'], 3);
			// общее время загрузки в js в секундах: Math.round(loadingPage) / 1000;
			// время загрузки страницы от начала HEAD и до конца скриптов в секундах: Math.round(loadingPage - loadingStart) / 1000;
			// время загрузки страницы от PHP запроса до начала HEAD в секунах: round(loadingStart - $_SERVER['REQUEST_TIME_FLOAT'], 3);
			
			// суммарное время загрузки:
			// php [ round(loadingStart - $_SERVER['REQUEST_TIME_FLOAT'], 3) ] + js [ Math.round(loadingPage - loadingStart) / 1000 ];
		?>
		
		loadingPage = 
			"PAGE LOADING STATISTIC\n" +
			"\n" +
			"php from request was loaded in <?= round($loadingPage - $_SERVER['REQUEST_TIME_FLOAT'], 3); ?> sec\n" +
			"js from DOMHighResTimeStamp was loaded in " + (Math.round(loadingPage) / 1000) + " sec\n" +
			"this page was loaded in " + Math.round( ( <?= round($loadingStart - $_SERVER['REQUEST_TIME_FLOAT'], 3); ?> + Math.round(loadingPage - loadingStart) / 1000 ) * 1000 ) / 1000 + " sec\n" +
			"... <?= round($loadingStart - $_SERVER['REQUEST_TIME_FLOAT'], 3); ?> sec [php timestamp js] " + (Math.round(loadingStart) / 1000) + " sec ...\n\n" +
			"this page size is " + (Math.round($('html').html().length / 1024)) + " Kb" +
			"<?php if (in('options', 'inspect')) { echo '\n\nisENGINE version ' . isENGINE . '\n\n' .
				$loadingLog . '\n' .
				objectToString(in('options'), ' option is enable,\n') . ' option is enable\n\n' .
				'current site: ' . $url -> host . '\n' .
				'current page: ' . thispage('is') . '\n' .
				'current folders: ' . (!empty(thispage('parents')) ? objectToString(thispage('parents'), ', ') : 'none') . '\n' .
				'current article: ' . set(objectGet('content', 'name'), true) . '\n' .
				'current params: ' . (!empty(in('parameters')) ? '[' . objectToString(in('parameters'), ', ', true) . ']' : 'none') . '\n' .
				'current lang: ' . $lang -> lang . '-' . $lang -> code . '\n\n' .
				'SID: ' . $_COOKIE['SID'] . ' (cookie session id)\n' .
				'UID: ' . $_COOKIE['UID'] . ' (cookie user personal id)\n' .
				'PID: ' . $_COOKIE['PID'] . ' (cookie private id)\n' .
				'AID: ' . $_COOKIE['AID'] . ' (cookie admin id)\n\n' .
				'protocol: ' . $_SERVER['REQUEST_SCHEME'] . '/' . $_SERVER['SERVER_PROTOCOL'] . '\n' .
				'server: ' . $_SERVER['SERVER_SOFTWARE'] . '\n' .
				'php version: ' . PHP_VERSION . ' (' . ((PHP_INT_SIZE == 4) ? '32' : ((PHP_INT_SIZE == 8) ? '64' : PHP_INT_SIZE . 'x8')) . '-bit)\n' .
				'php memory: ' . datanum(memory_get_usage() / 1024, 'bits') . ' Mb\n' .
				'php max memory: ' . datanum($loadingMemory / 1024, 'bits') . ' Mb (before template) - ' . datanum(memory_get_peak_usage() / 1024, 'bits') . ' Mb (after template)\n' .
				'php extensions: ' . objectToString(get_loaded_extensions(), ', ') .
			''; } ?>";
		
		var div = document.createElement('div');
		div.id = "inspect";
		div.style.setProperty("display", "none", "important");
		div.innerHTML = '<!--\n' + loadingPage + '\n-->';
		document.body.appendChild(div);
		
		console.log(loadingPage);
		
	};
</script>

<!-- page was generated in <?= round($loadingPage - $_SERVER['REQUEST_TIME_FLOAT'], 3); ?> sec -->
<!-- for more information see in javascript console -->
