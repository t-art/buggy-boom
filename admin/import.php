<?
// ---------------------ФУНКЦИИ-----------------------

function parse_excel_file( $filename ){
	// подключаем библиотеку
	require_once $_SERVER['DOCUMENT_ROOT'].'/inc/advanced/PHPExcel/PHPExcel.php';
	
	$result = array();
	
	// получаем тип файла (xls, xlsx), чтобы правильно его обработать
	$file_type = PHPExcel_IOFactory::identify( $filename );
	// создаем объект для чтения
	$objReader = PHPExcel_IOFactory::createReader( $file_type );
	$objPHPExcel = $objReader->load( $filename ); // загружаем данные файла в объект
	$result = $objPHPExcel->getActiveSheet()->toArray(); // выгружаем данные из объекта в массив
	
	return $result;
}

  function getXLS($xls){
    include_once $_SERVER['DOCUMENT_ROOT'].'/inc/advanced/PHPExcel/PHPExcel/IOFactory.php';
    $objPHPExcel = PHPExcel_IOFactory::load($xls);
    $objPHPExcel->setActiveSheetIndex(0);
    $aSheet = $objPHPExcel->getActiveSheet();
 
    //этот массив будет содержать массивы содержащие в себе значения ячеек каждой строки
    $array = array();
    //получим итератор строки и пройдемся по нему циклом
    foreach($aSheet->getRowIterator() as $row){
      //получим итератор ячеек текущей строки
      $cellIterator = $row->getCellIterator();
      //пройдемся циклом по ячейкам строки
      //этот массив будет содержать значения каждой отдельной строки
      $item = array();
      $nom=0;
      foreach($cellIterator as $cell){
        $nom++;
        if ($nom<4 || $nom>19) continue;
        //заносим значения ячеек одной строки в отдельный массив
        array_push($item, iconv('utf-8', 'cp1251', $cell->getCalculatedValue()));
      }
      //заносим массив со значениями ячеек отдельной строки в "общий массв строк"
      array_push($array, $item);
    }
    return $array;
  }


// бывает что функция fgetcsv глючит, тогда меняем ее на считывание построчно (fgets): while($line = fgets($file)) { $row = str_getcsv4($line);
// ПЕРВЫМ ДЕЛОМ ПОПОРОБОВАТЬ: setlocale(LC_ALL, 'ru_RU.CP1251');
function str_getcsv4($input, $delimiter = ';', $enclosure = '"') 
{
	if(!preg_match("/[$enclosure]/", $input))
		return (array)preg_replace(array("/^\\s*/", "/\\s*$/"), '', explode($delimiter, $input));
	
	$token = "##"; 
	$token2 = "::";
	//alternate tokens "\034\034", "\035\035", "%%";
	$t1 = preg_replace(array("/\\\[$enclosure]/", "/$enclosure{2}/", "/[$enclosure]\\s*[$delimiter]\\s*[$enclosure]\\s*/", "/\\s*[$enclosure]\\s*/"), array($token2, $token2, $token, $token), trim(trim(trim($input), $enclosure)));
	
	$a = explode($token, $t1);
	foreach($a as $k=>$v) 
		if(preg_match("/^{$delimiter}/", $v) || preg_match("/{$delimiter}$/", $v))
		{
			$a[$k] = trim($v, $delimiter); 
			$a[$k] = preg_replace("/$delimiter/", "$token", $a[$k]); 
		}
	$a = explode($token, implode($token, $a));
	return (array)preg_replace(array("/^\\s/", "/\\s$/", "/$token2/"), array('', '', $enclosure), $a);
}

// бэкап таблиц
function makeBackup($tbl)
{
	global $prx;
    
	$tim=time();
    
    mysql_query("DROP TABLE IF EXISTS {$prx}{$tbl}_bak{$tim}");
	mysql_query("CREATE TABLE {$prx}{$tbl}_bak{$tim} LIKE {$prx}{$tbl}");
	mysql_query("INSERT INTO {$prx}{$tbl}_bak{$tim} SELECT * FROM {$prx}{$tbl}");
    
    return $prx.$tbl.'_bak'.$tim;
}

function log_bak($tbl)
{
  $tim=time();
}

// ПОЛУЧАЕТ ОДНО ЗНАЧЕНИЕ ИЗ ТАБЛИЦЫ
function getField($sql)
{
	$res = sql($sql); 
	$field = @mysql_result($res,0,0);
	return $field;
}

// ЗАМЕНА mysql_query - ВЫВОДИТ ТЕКСТ ЗАПРОСА В СЛУЧАИ НЕУДАЧИ
function sql($sql, $debug=false)
{
	global $debugSql, $ajaxSql;
	$res = mysql_query($sql);
  return $res;
}

function tolog($str,$type='',$tbl='')
{
  global $prx;
  $min_id=getField("select min(id) from {$prx}log_import");
  $count=getField("select count(id) from {$prx}log_import");
  
 if ($count>15)
 {
   $ch_tb=getField("select bak_tbl from {$prx}log_import where id='{$min_id}'");
   mysql_query("drop {$ch_tb}");
   mysql_query("delete from {$prx}log_import where id='{$min_id}'");
 }     
 
  mysql_query("insert into {$prx}log_import set log_import='{$str}',date=NOW(),type='{$type}',bak_tbl='{$tbl}'");
  
  return mysql_insert_id();
}

// ------------------ИНИЦИАЛИЗАЦИЯ--------------------
$rubric_img = 3008;
$rubric = 'Экспорт/Импорт';
$id = (int)@$_GET['id'];
$action=$_GET['action'];

	$mysql_conn = array('host'=>'localhost',	'login'=>'c6623_tecsvet', 'pwd'=>'gF638Fhsj37F4FHGs8', 'db'=>'c6623_tecsvet');
	$dblink = @mysql_connect($mysql_conn['host'],$mysql_conn['login'],$mysql_conn['pwd'],$mysql_conn['db']) or exit('Database connection error');
	mysql_select_db($mysql_conn['db'], $dblink) or exit('Database not found');
	header('Content-Type: text/html; charset=utf-8');
	mysql_query("SET NAMES utf8");
	//setlocale(LC_ALL, 'ru_RU.UTF-8');
	mb_internal_encoding('UTF-8');
	$prx = 'teksvet_';


// -------------------СОХРАНЕНИЕ----------------------
switch($action)
{
    case 'write_features':
       
       require_once $_SERVER['DOCUMENT_ROOT'].'/inc/simple_html_dom.php';
       $features_res=mysql_query("select * from {$prx}ref_feature");
       while ($r=mysql_fetch_array($features_res))
         $features[$r['id']]=$r['name'];
       
        //создаём новый объект
        $html = new simple_html_dom();

        $tbl='ref_product';
       $res=mysql_query("select * from {$prx}{$tbl} where url_feature!='' LIMIT 3");
       if (mysql_num_rows($res)==0) 
       {
         ?>
          <script>
            alert("Все характеристики выгружены");
            top.location.href="index.php?request=feature/list";
          </script>
         <?
         exit();
       }
       
       while ($row=mysql_fetch_array($res))
       {
         $url=$row['url_feature'];
         $html=file_get_html($url);
         
        if ($html)
         foreach($html->find('ul.section-item-property-content ul li') as $element)
         { 
           if (!$element->find('span',0) || !$element->find('span',1)) continue;
            
           $feat1=iconv('windows-1251','utf-8',$element->find('span',0)->innertext());
           $feat1_val=iconv('windows-1251','utf-8',$element->find('span',1)->innertext());
           
           if (!$feat1 || !$feat1_val) continue;
           
           if (!in_array($feat1,$features))
           {
             mysql_query("insert into {$prx}ref_feature set name='{$feat1}', in_listing=1, type='list'");
             $cur_feature=mysql_insert_id();
             $features[$cur_feature]=$feat1;
           }
           else
           {
             $cur_feature=array_search($feat1,$features);
           }
           
         $cat=mysql_query("select category_id from {$prx}link_product_vs_category where product_id='{$row['id']}' ");
         
         while ($r2=mysql_fetch_array($cat))
         {
           $category_id=$r2['category_id']; 
           mysql_query("INSERT INTO {$prx}link_category_vs_feature SET category_id = '{$category_id}', feature_id='{$cur_feature}'");
         }
           
           mysql_query("INSERT INTO {$prx}link_product_vs_feature SET product_id = '{$row['id']}', feature_id='{$cur_feature}', value_id='0', value_manual='{$feat1_val}'"); 
           //echo iconv('windows-1251','utf-8',$element->find('span',0)->innertext()). '<br>';
           //echo iconv('windows-1251','utf-8',$element->find('span',1)->innertext()). '<br>';
         }
         
         mysql_query("update {$prx}{$tbl} set url_feature='' where id='{$row['id']}'");  
       }
    
      ?>
       <script>location.href='import.php?action=write_features'</script>
      <?
    
    break;
    
    
	case 'import_goods':

    $result['insert']=0;
    $result['update']=0;

       $tbl='ref_product';
       $fields = array('url_feature');
      
       $xlsData =  parse_excel_file($_FILES['file_import_goods']['tmp_name']); //извлеаем данные из XLS
       $new_m = array();
          
         foreach ($xlsData as $n=>$mas)
         {
           if ($n<2) continue;
           
           foreach ($mas as $i=>$row)
           {
        //     if ($row!='') 
              $n_m[$i]=$row;
           } 
           
           $all_mass[]=$n_m;
         } 
          
         $flag=false;
         $num=0;

		$results = array();
		$bak_tbl=makeBackup($tbl);
		$withoutDebugSql = true; // отключаем вывод sql - ошибки 
		
		setlocale(LC_ALL, 'ru_RU.CP1251');
//		$file = fopen($_FILES['file']['tmp_name'], 'r');
		$start = 1; 
		$line = 0;
         
         foreach ($all_mass as $m=>$val)
         {
              $id=$val[0];
              $article=$val[1];
              $url_feature=$val[2];
              $category_id=$val[3];

         	 $set = array();
				foreach($fields as $key=>$val)
				{
					$set[] = "`{$val}`='".iconv('windows-1251','utf-8',$$val)."'";
				}
			
				$id_new = mysql_query("update {$prx}{$tbl} set ".implode(',', $set)." where id='{$id}'");
                
                
				if($id_new)
				{
					$result[$id ? 'update' : 'insert']++;
				}
				else
					$results['false']++;
			}

     $log_import="{$pr_log} Импорт товаров, ".date('H:i d.m')." ,обновлено - {$result['update']}, добавлено - {$result['insert']}";
     $log_id=tolog($log_import,'ref_product',$bak_tbl);

     $log_import.="&nbsp;&nbsp;<a href='export_import.php?id_log={$log_id}&action=bak' target='iframe'>откатить изменения</a><br><hr>";

		?><script>
            location.href='import.php?action=write_features';
          /*  
            top.document.getElementById('log_info').innerHTML="<?=$log_import?>"+top.document.getElementById('log_info').innerHTML;
			top.topBack(true);
          */  
		</script><?
		exit;	


	case 'import_goods_title':

    $result['update']=0;

       $tbl='ref_product';
      
       $xlsData =  parse_excel_file($_FILES['file_import_goods']['tmp_name']); //извлеаем данные из XLS
       $new_m = array();
          
         foreach ($xlsData as $n=>$mas)
         {
           //if ($n<2) continue;
           
           foreach ($mas as $i=>$row)
           {
        //     if ($row!='') 
              $n_m[$i]=$row;
           } 
           
           $all_mass[]=$n_m;
         } 
          
         $flag=false;
         $num=0;

		$results = array();
		$bak_tbl=makeBackup($tbl);
		$withoutDebugSql = true; // отключаем вывод sql - ошибки 
		
		setlocale(LC_ALL, 'ru_RU.CP1251');
//		$file = fopen($_FILES['file']['tmp_name'], 'r');
		$start = 1; 
		$line = 0;
         
        $fields=array('meta_title'); 
         foreach ($all_mass as $m=>$val)
         {
              $id=$val[0];
              $article=$val[2];
              $meta_title=$val[3];

         	 $set = array();
				foreach($fields as $key=>$val)
				{
					$set[] = "`{$val}`='".$$val."'";
				}
			
				$id_new = mysql_query("update {$prx}{$tbl} set ".implode(',', $set)." where id='{$id}'");
                
                
				if($id_new)
				{
					$result['update']++;
				}
				else
					$results['false']++;
			}

     $log_import="{$pr_log} Импорт товаров, ".date('H:i d.m')." ,обновлено - {$result['update']}";
     $log_id=tolog($log_import,'ref_product',$bak_tbl);

     $log_import.="&nbsp;&nbsp;<a href='export_import.php?id_log={$log_id}&action=bak' target='iframe'>откатить изменения</a><br><hr>";

		?><script>
          /*  
            top.document.getElementById('log_info').innerHTML="<?=$log_import?>"+top.document.getElementById('log_info').innerHTML;
			top.topBack(true);
          */  
		</script><?
		exit;


	// откат импорта
	case 'bak':
	
       $bak_info=getRow("select * from {$prx}log_import where id='{$_GET['id_log']}'");	
        
       $tbl=$prx.$bak_info['type']; 
        
        if(!$tbl)
			errorAlert('Выберите таблицу');
		if(!mysql_num_rows(sql("SHOW TABLES LIKE '{$bak_info['bak_tbl']}'")))
			errorAlert('Нет данных для отката', 1);
		
        mysql_query("DROP TABLE IF EXISTS {$tbl}");
		mysql_query("RENAME TABLE {$bak_info['bak_tbl']} TO {$tbl}");


		//---удалить из логов + удалить более поздние таблицы импорта-------
          $c_d=$bak_info['date'];
          $model_type=$bak_info['type'];
          
          $tables=mysql_query("select id,bak_tbl from {$prx}log_import where date>='{$c_d}' and type='{$model_type}'");
          while ($row_t=mysql_fetch_array($tables))
          {
            mysql_query("delete from {$prx}log_import where id='{$row_t['id']}'");
            mysql_query("DROP TABLE IF EXISTS {$row_t['bak_tbl']}");
          }
        //------------------------------------------------------------------


		?><script>
            alert('Откат успешно завершен')        
			top.topReload();
		</script><?
		exit;		
}
if($action)	exit;


// ----------------------ВЫВОД------------------------
ob_start();
switch($show)
{
	default: // просмотр
	?>
		<table>
			<tr valign="top">
				<td>
                  <div style="margin: 40px 0px;" id="log_info">
                    <?
                      $lg=mysql_query("select * from {$prx}log_import order by id DESC");
                      while ($row_lg=mysql_fetch_array($lg))
                      {
                        echo $row_lg['log_import'].'&nbsp;&nbsp;<a href="export_import.php?id_log='.$row_lg['id'].'&action=bak" target="iframe">откатить изменения</a><br>';
                        echo '<hr>';
                      }
                    ?>
                  </div>
                </td>
                <td style="padding-left:40px;">
                	<form action="?action=import_goods" target="iframe" method="post" enctype="multipart/form-data">
						<table class="content" width="480">
							<tr>
								<th><span class="la16" style="background-position:0 -448px;"></span> Импортировать товары</th>
							</tr>
                            
                            <tr><td>PriceForSite.xls</td></tr>
							<tr>
								<td>
									<table class="content" width="100%">
										<tr>
											<td class="fwn">Файл xls</td>
											<td><input type="file" name="file_import_goods"></td>
										</tr>
									</table>
									<div align="center" style="font-size:10px; margin-top:5px;">*Обновляются все товары + записываются новые.</div>
								</td>
							</tr>                            
							<tr>
								<th style="text-align:right;"><input type="submit" value="Загрузить" /></th>
							</tr>                            
                            
                         </table>
                      </form>                  
                	<form action="?action=import_goods_title" target="iframe" method="post" enctype="multipart/form-data">
						<table class="content" width="480">
							<tr>
								<th><span class="la16" style="background-position:0 -448px;"></span> Импортировать товары</th>
							</tr>
							<tr>
								<td>
									<table class="content" width="100%">
										<tr>
											<td class="fwn">Файл xls</td>
											<td><input type="file" name="file_import_goods"></td>
										</tr>
									</table>
									<div align="center" style="font-size:10px; margin-top:5px;">*Обновляются титлы товаров.</div>
								</td>
							</tr>                            
							<tr>
								<th style="text-align:right;"><input type="submit" value="Загрузить" /></th>
							</tr>                            
                            
                         </table>
                      </form>                  
                
				</td>
			</tr>
		</table>		
	<?	break;
}
$content = ob_get_clean();

print_r($content);

?>