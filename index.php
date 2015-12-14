<?php
	require '/lib/simple_html_dom.php';
	  $in = json_decode(file_get_contents('php://input'), true);
	  $file = file_get_contents('http://jvndb.jvn.jp/ja/rss/jvndb_new.rdf',LIBXML_NOCDATA);
	  $xml = simplexml_load_string($file);
	  $csv = array(); //csvを保存。
	  //ファイル名を生成
	  $today = date("Y-m-d-H-i");
	  $filename = $today.".csv";
	  if($xml){
		 $row = array();
		 for($i = 0; $i < Count($xml->item); $i++){
			if($xml->item[$i]->children("sec",true)->cvss->attributes()->score >= 5){
				$link = "";
				$product="";
				$releaseDate="";
				$link =  senitize($xml->item[$i]->link);	
				$html = file_get_html($link);
				if($html){
					$releaseDate = $html->find('td[class=vuln_table_clase_date_header_td]',0)->next_sibling ()->text();
					$version = $html->find('td[class=vuln_table_clase_td_header]',2)->parent()->next_sibling()->next_sibling()->text();
				}
				//ID
				$row[]=senitize($xml->item[$i]->children('dc',true)->identifier);
				//製品
				$row[] = '';
				//製品version
			    $row[] = senitize($version);
				//詳細
				$row[] = senitize($xml->item[$i]->description);
				//判断
				$row[] = '';
				//理由
			    $row[] = '';
				//リンク
				$row[] = senitize($link);
				//公開日
				$row[] = senitize($releaseDate);
				$csv[] = $row;
				$row = array();
			} 
		 }
		 
		 $fp = fopen($filename,"w");
		 foreach($csv as $row){
			 fputcsv($fp,$row);
		 } 
		 fclose($fp);
	  }
	  
	  function senitize($value){
		  $value = str_replace(',', '', $value);
		  $value = str_replace("\n", '', $value);
		  $value = str_replace("&#13", '', $value);
		  $value = str_replace("\r", '', $value);
		  $value = str_replace("&nbsp;", '', $value);
		  return $value;
	  }
	  
