<?php 

header('Content-Type:text/html; charset=utf-8'); 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class Api
{
	public function start_loads()
	{
		$action_ids = $this->get_argv('action_ids');
		$date_1 = $this->get_argv('date_1');
		$date_2 = $this->get_argv('date_2');
		$bp_api_key = $this->get_argv('bp_api_key');
		$limit = $this->get_argv('limit');
		$max = $this->get_argv('max');
		$only_count = $this->get_argv('only_count');
		$start_page = $this->get_argv('start_page');
		$stop_page = $this->get_argv('stop_page');
		$only_blue = $this->get_argv('only_blue');
		$set_status = $this->get_argv('set_status');

		if($action_ids == false)
		{
			echo 'action_ids not exists'."\n";
			return false;
		}

		if($date_1 == false)
		{
			echo 'date_1 not exists'."\n";
			return false;
		}

		if($date_2 == false)
		{
			echo 'date_2 not exists'."\n";
			return false;
		}

		if($bp_api_key == false)
		{
			echo 'bp_api_key not exists'."\n";
			return false;
		}

		if($start_page == false)
		{
			$start_page = 0;
		}

		$dop_params = '';
		if($only_blue !== false)
		{
			$dop_params .= '&only_blue=true';
		}

		if($set_status !== false)
		{
			$dop_params .= '&set_status='.$set_status;
		}

		if($limit == false)
		{
			$limit = 10;
		}

		$json = file_get_contents(
			"https://back.crm.acsolutions.ai/api/v2/test/test_commands?".
			"act=get_order_phone_by_action_api".
			"&api_key=WKLnen@KJNeE2keE3!mlee2".
			"&action_ids=".urlencode($action_ids).
			"&date_1=".urlencode($date_1).
			"&date_2=".urlencode($date_2).
			"&api=".urlencode($bp_api_key).
			"&data_count=".urlencode($bp_api_key).
			$dop_params
		);

		$data_arr = json_decode($json,true);

		if(!is_array($data_arr) || !isset($data_arr['data']))
		{
			if(isset($data_arr['msg']))
			{
				echo $data_arr['msg']."\n";
			}else{
				echo "Произошла ошибка отправки запроса.."."\n";
			}
		
			return false;
		}

		if($data_arr['data'] == 0)
		{
			echo "Нет данных по запросу."."\n";
			return false;
		}

		if($data_arr['data'] >= $limit)
		{
			$pages = (int) ( $data_arr['data'] / $limit );
		}else{
			$pages = 1;
		}

		

		echo "Всего лидов: ".$data_arr['data']."\n";
		echo "Кол-во страниц: ".$pages."\n";

		if($only_count !== false)
		{
			exit;
		}

		$page = $start_page;
		$count_send = 0;
		$is_stop = false;
		while (true)
		{
			echo "Получение страницы: ".$page."\n";


			$json2 = file_get_contents(
				"https://back.crm.acsolutions.ai/api/v2/test/test_commands?".
				"act=get_order_phone_by_action_api".
				"&api_key=".urlencode("WKLnen@KJNeE2keE3!mlee2").
				"&action_ids=".urlencode($action_ids).
				"&date_1=".urlencode($date_1).
				"&date_2=".urlencode($date_2).
				"&api=".urlencode($bp_api_key).
				"&page222=".urlencode($page).
				"&limit22=".urlencode($limit).
				$dop_params
			);

			//echo $json2."\n";

			$page++;

			$arr = json_decode($json2,true);

			if(!is_array($arr))
			{
				echo 'Произошла ошибка запроса.'."\n";
				$is_stop = true;
			}

			if(!isset($arr['msg']))
			{
				echo 'Получен не ожиданный ответ.'."\n";
				$is_stop = true;
			}

			if(is_array($arr) && isset($arr['data']))
			{
				$count_send = $count_send+(int)$arr['data'];
			}

			echo "Загружено контактов: ".$count_send."\n";

			if($stop_page !== false && $stop_page == $page)
			{
				$is_stop = true;
			}

			if($page > $pages) $is_stop = true;

			if($is_stop == true) break;



			//echo $arr['msg']."\n";			
		}		

		echo "==========================================\n";
		echo "Всего переданных данных: ".$count_send."\n";
		echo "Процесс завершен.\n";
	}

	public function get_argv($search = '')
	{
		foreach ($_SERVER['argv'] as $argv)
		{
			if(strstr($argv,"--".$search."="))
			{
				$data = str_replace("--".$search."=", '', $argv);
				$data = str_replace("'", '', $data);
				return $data;
			}
		}

		return false;
	}
}

$arr = new Api;

if(!isset($_SERVER['argv']) || !isset($_SERVER['argv'][1])) exit('no command'."\n");

if($_SERVER['argv'][1] == 'loads')
{
	$arr->start_loads();
}else{
	exit('command not exists'."\n");
}
?>