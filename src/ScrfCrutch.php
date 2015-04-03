<?php
namespace KimonoCrutch;

class ScrfCrutch extends BaseCrutch {
    protected $index_url = 'http://www.scrf.gov.ru/news/19/';

    protected function get_links($html) {
        $links = qp($html, 'div#news>div.news_block>a');
        foreach($links as $i) {
            $result[] = array(
                'title' => $i->text(),
                'url' => 'http://www.scrf.gov.ru' . $i->attr('href')
            );
        }
        return $result;
    }

    protected function get_detail($html) {
        return array(
            'title' => qp($html, 'div#content>div#news>h2')->text(),
            'fulltext' => qp($html, 'div#content>div#news>div.news_block')->text()
        );
    }
}

