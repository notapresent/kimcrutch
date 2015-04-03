<?php
namespace KimonoCrutch;

class McxCrutch extends BaseCrutch {
    public function __construct(){
        $this->qpopts = array(
            'convert_to_encoding' => 'utf-8',
        );
    }
    protected $site_charset = 'windows-1251';
    protected $index_url = 'http://www.mcx.ru/navigation/newsfeeder/show/78.htm';

    protected function get_links($html) {
        $result = array();

        $links = htmlqp($html, 'span.BlackLink>a.BlackLink', $this->qpopts);
        foreach($links as $i) {
            $result[] = array(
                'title' => $i->text(),
                'url' => 'http://www.mcx.ru' . $i->attr('href')
            );
        }
        return $result;
    }

    protected function get_detail($html) {
        return array(
            'title' => htmlqp($html, 'div.ContentTitle', $this->qpopts)->text(),
            'fulltext' => htmlqp($html,'div.ContentText', $this->qpopts)->text()
        );
    }
}

