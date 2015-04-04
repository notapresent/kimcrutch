<?php
namespace KimonoCrutch;

class MilCrutch extends BaseCrutch {
    protected $index_url = 'http://dyn.function.mil.ru/news_page/country.htm';
    public function __construct(){
        $this->qpopts = array(
            'convert_to_encoding' => 'utf-8',
        );
    }

    protected function get_links($html) {
        $result = array();

        $links = htmlqp($html, 'div#center>div.newsitem>a', $this->qpopts);
        foreach($links as $i) {
            $result[] = array(
                'title' => $i->text(),
                'url' => $i->attr('href')
            );
        }
        return $result;
    }

    protected function get_detail($html) {
        $title = htmlqp($html, 'div#content>div#center>h1', $this->qpopts)->text();
        $fulltext = htmlqp($html,'div#content>div#center', $this->qpopts)->text();
        $fulltext = mb_substr(
            $fulltext,
            mb_strpos($fulltext, $title) + mb_strlen($title)
        );
        if(mb_strpos($fulltext, 'Метки:')) {
            $fulltext = mb_substr($fulltext, 0, mb_strpos($fulltext, 'Метки:'));
        }
        if(mb_strpos($fulltext, 'Идет получение информации...')) {
            $fulltext = mb_substr($fulltext, 0, mb_strpos($fulltext, 'Идет получение информации...'));
        }
        
        return array(
            'title' => $title,
            'fulltext' =>  $fulltext
        );
    }
}

