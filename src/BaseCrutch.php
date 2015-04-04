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
            $url = htmlentities($this->make_url($link['url']));
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
        $html = $this->fetch_and_gunzip($url);
        
        if($this->site_charset != 'utf-8') {
            $html = str_ireplace("charset={$this->site_charset}","charset=utf-8", $html);
            $html = mb_convert_encoding($html, 'utf-8', $this->site_charset);
        }
        
        return $html;
    }

    protected function make_url($ourl) {
        $fqcn = get_class($this);
        $classname = trim(substr($fqcn, strrpos($fqcn, '\\')), '\\');
        $alias = strtolower(substr($classname, 0, -6));
        $self = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['SCRIPT_NAME']}";
        return "{$self}?alias={$alias}&action=detail&url=" . urlencode($ourl);
    }
    
    protected function fetch_and_gunzip($url) {
        $headers = array(
            'Accept: text/html',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4,de;q=0.2',
            'Cache-Control: max-age=0',
            'Connection: close',
            'User-Agent: Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko)'        
        );
        
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => implode("\r\n", $headers)
            )
        );
     
        $context = stream_context_create($opts);
        $content = file_get_contents($url ,false, $context); 
         
        //If http response header mentions that content is gzipped, then uncompress it
        foreach($http_response_header as $c => $h) {
            if(stristr($h, 'content-encoding') and stristr($h, 'gzip')) {
                //Now lets uncompress the compressed data
                $content = gzinflate( substr($content,10,-8) ) . '<!-- gunzipped -->';
            }
        }
         
        return $content;        
    }

    abstract protected function get_links($html);
    abstract protected function get_detail($html);
}

