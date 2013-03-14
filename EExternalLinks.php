<?php
/**
 * EExternalLinks class file.
 *
 * @author Vladimir Papaev <kosenka@gmail.com>
 * @version 0.1
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @github https://github.com/kosenka/EExternalLinks
 */

/*
$el = Yii::createComponent('application.extensions.EExternalLinks');
$text=$el->run($text);
*/

class EExternalLinks
{
        public $set_noindex=true;
        public $set_title=false;
        public $set_blank=true;
        public $set_nofollow=true;

        public function init()
        {
        }

	public function __construct()
	{
	}

        public function run($data)
        {
        	preg_match_all('#<a .*?href=([\"\'])((https?|ftp):\/\/\S*?)\\1.*?>.*?<\/a>#im', $data, $arr, PREG_PATTERN_ORDER);

        	for ($i =0 ; $i<count($arr[0]); $i++)
        	{
        		//print(htmlspecialchars($arr[0][$i]));

        		$kv = $arr[1][$i]; // кавычка
        		// $arr[2][$i] - весь адрес, с http://
        		// $arr[0][$i] - вся ссылка
        		if ( $this->link2update($arr[2][$i], $arr[0][$i]) )
        		{
        			$tmp = str_replace($kv.$arr[2][$i].$kv, $kv."/external/".base64_encode($arr[2][$i]).$kv, $arr[0][$i]);

        			// изменяем target="_blank", только если он еще не выставлен
        			if ( $this->set_blank && !stristr($tmp, '"_blank"') )
        				$tmp = str_replace('<a', '<a target="_blank"', $tmp);

        			// изменяем title, только если он еще не выставлен
        			if ( $this->set_title && !preg_match("/(\btitle|\bTITLE)\s*?=\s*?[\"\'].*?[\"\']/", $tmp) )
        				$tmp = str_replace('<a', '<a title="'.$arr[2][$i].'"',$tmp);

        			// изменяем nofollow, только если он еще не выставлен
        			if ( $this->set_nofollow && !preg_match("/\brel\s*?=\s*?[\"\']nofollow[\"\']/i", $tmp) )
        				$tmp = str_replace('<a', '<a rel="nofollow"',$tmp);

        			if ($this->set_noindex)
        				$tmp = '<!--noindex-->'.$tmp.'<!--/noindex-->';

        			// заменяем все вхождения текущей ссылки в тексте $data на ее преобразованную версию
        			$data = str_replace($arr[0][$i], $tmp, $data);
        		}
        	}
        	return $data;
        }
        
        protected function link2update($link_href, $link_full )
        {
                // проверка href-части ссылки на принадлежность к адресу блога
                if ( ereg('^http://'.$_SERVER['HTTP_HOST'], $link_href) ) return false;

                // проверка href-части ссылки на принадлежность к stop-листу
                /*$stop_list = explode("\n", $jexr_opt['jex_stop']);
                foreach ($stop_list as $t)
                        if (stripos($link_href, trim($t)) !== false) return false;
                */

                //if ( !empty($link_full) ) // задан второй параметр
                /*{
                        // Проверка на то, что в полной ссылке содержится class="<что-то там>", rel="<что-то там>" или javascript
                        if ( preg_match("/(\brel|\bREL)\s*?=\s*?[\"\']".$jexr_opt['jex_rel']."[\"\']/", $link_full)
                                || preg_match("/(\bclass|\bCLASS|\bClass)\s*?=\s*?[\"\']".$jexr_opt['jex_class']."[\"\']/", $link_full)
                                || stristr($link_full, 'javascript:') )
                                return false;
                }*/
                return true;
        }
}
