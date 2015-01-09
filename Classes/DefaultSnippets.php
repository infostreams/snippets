<?php
namespace Phile\Plugin\Infostreams\Snippets;

class DefaultSnippets extends Snippets {

	/**
	 * Implements the 'link' snippet
	 */
	public function link($link, $text=null, $title=null, $popup=false, $class=null) {
		if (is_null($text)) {
			// if no text is given, we use the text originally provided by the user
			$text = $link;
		}

		// deal with internal and (slightly) malformed links
		$link = $this->getLink($link);

		$attributes = $this->getHtmlAttributes(array(
			'title'=>$title,
			'target'=>$this->isTrue($popup)?"blank":null,
			'class'=>$class
		));

		return "<a href='$link'$attributes>$text</a>";
	}

	/**
	 * Implements the 'email' snippet
	 */
	public function email($address, $text=null, $title=null, $class=null) {
		if (is_null($text)) {
			$text = $address;
		}
		$attributes = $this->getHtmlAttributes(array(
			'title'=>$title,
			'class'=>$class
		));
		return "<a href='mailto:$address'$attributes>$text</a>";
	}

	/**
	 * Implements the 'tel' snippet
	 */
	public function tel($nr, $text=null, $class=null) {
		if (is_null($text)) {
			$text = $nr;
		}
		$attributes = $this->getHtmlAttributes(array('class'=>$class));
		return "<a href='tel:$nr'$attributes>$text</a>";
	}

	/**
	 * Implements the 'image' snippet
	 */
	public function image($image, $width=null, $height=null, $alt=null, $class=null, $link=null, $caption=null, $srcset=null) {
		$image = $this->getLink($image);
		$attributes = $this->getHtmlAttributes(array(
			'width'=>$width,
			'height'=>$height,
			'alt'=>$alt,
			'caption'=>$caption,
			'class'=>$class,
			'srcset'=>$srcset
		));

		$img = "<img src='$image'$attributes />";
		if (!is_null($link)) {
			$link = $this->getLink($link);
			return "<a href='$link'>$img</a>";
		}

		return $img;
	}

	/**
	 * Implements the 'file' snippet
	 */
	public function file($file, $text=null, $download=null, $class=null) {
		if (is_null($text)) {
			// if no text is given, we use the text originally provided by the user
			$text = $file;
		}
		$file = $this->getLink($file);
		$attributes = $this->getHtmlAttributes(array('class'=>$class));

		if ($this->isTrue($download)) {
			// try to force download - http://stackoverflow.com/a/21527905/426224
			$attributes .= " download target='_blank'";
		}

		return "<a href='$file'$attributes>$text</a>";
	}

	/**
	 * Implements the 'youtube' snippet
	 */
	public function youtube($link, $width=480, $height=360) {
		$url = parse_url($link);
		if (!array_key_exists('query', $url)) {
			return "";
		}
		parse_str($url['query'], $query);
		$is_playlist = array_key_exists('list', $query);
		if ($is_playlist) {
			$playlist_id = $query['list'];
			$embed_url = "//www.youtube.com/embed?listType=playlist&list=$playlist_id";
		} else {
			$video_id = $query['v'];
			$embed_url = "//www.youtube.com/embed/$video_id";
		}

		return "<iframe width=\"$width\" height=\"$height\" src=\"$embed_url\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
	}

	/**
	 * Implements the 'vimeo' snippet
	 */
	public function vimeo($link, $width=480, $height=360, $portrait=true, $title=true, $byline=true) {
		$matches = array();
		$regexp = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*(?\'video_id\'[0-9]{6,11})[?]?.*/';
		if (preg_match_all($regexp, $link, $matches)) {
			if (array_key_exists('video_id', $matches)) {
				$embed = "//player.vimeo.com/video/" . $matches['video_id'][0];

				$opts = array();
				if (!$this->isTrue($portrait)) { $opts[] = "portrait=0"; }
				if (!$this->isTrue($title))    { $opts[] = "title=0"; }
				if (!$this->isTrue($byline))   { $opts[] = "byline=0"; }
				if (count($opts)>0) {
					$embed .="?" . implode("&amp;", $opts);
				}

				return "<iframe width=\"$width\" height=\"$height\" src=\"$embed\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";
			}
		}
	}

	/**
	 * Implements the 'twitter' snippet
	 */
	public function twitter($what, $text=null, $class=null) {
		if (is_null($text)) {
			$text = $what;
		}

		$attributes = $this->getHtmlAttributes(array('class'=>$class));

		if (substr($what,0,1)=='@') {
			$link = "https://www.twitter.com/" . ltrim($what, '@');
		} elseif (substr($what,0,1)=='#') {
			$parts = explode(' ', $what);
			if (count($parts)>1) {
				$link = "https://twitter.com/search?q=" . urlencode($what);
			} else {
				$link = "https://www.twitter.com/hashtag/" . ltrim($what, '#');
			}
		}

		return "<a href='$link'$attributes>$text</a>";
	}

	/**
	 * Implements the 'gist' snippet
	 */
	public function gist($link, $file=null) {
		$matches = array();
		if (preg_match('/[0-9]+/', $link, $matches)>0) {
			$gist_id = $matches[0];
			$link = "https://gist.github.com/$gist_id.js";
			if (!is_null($file)) {
				$link .= "?file=" . urlencode($file);
			}
		}

		return "<script src='$link'></script>";
	}

}