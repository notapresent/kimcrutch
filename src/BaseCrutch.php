<?php
namespace KimonoCrutch;

abstract class BaseCrutch {
    protected $doc_template = <<<EOB
<html>
<head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"/></head>
<body>{#BODY#}</body>
</html>
EOB;
    protected $site_charset = 'utf-8';

    public function index() {
        $html = $this->fetch_html($this->index_url);
        $links = $this->get_links($html);
        $body = '';
        foreach($links as $link) {
            $url = $this->make_url($link['url']);
            $body .= "<li><a href=\"{$url}\">{$link['title']}</a></li>\n";
        }
        return str_replace('{#BODY#}', "<ul>\n{$body}\n</ul>", $this->doc_template);
    }

    public function detail($url) {
        $html = $this->fetch_html($url);
        $detail = $this->get_detail($html);
        $body = "<h1 id=\"kimcrutch_title\">{$detail['title']}</h1>";
        $body .= "<div id=\"kimcrutch_fulltext\">{$detail['fulltext']}</div>";
        return str_replace('{#BODY#}', "\n{$body}\n", $this->doc_template);
    }

    public function fetch_html($url) {
        $html = file_get_contents($url);
        if($this->site_charset != 'utf-8') {
            $html = mb_convert_encoding($html, 'utf-8', $this->site_charset);
        }

        return $html;
    }

    protected function make_url($ourl) {
        $alias = strtolower(substr(get_class($this), 0, -6));
        $self = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}";
        return "{$self}?alias={$alias}&action=detail&url=" . urlencode($ourl);
    }

    abstract protected function get_links($html);
    abstract protected function get_detail($html);
}

