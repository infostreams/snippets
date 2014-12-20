<?php
namespace Phile\Plugin\Infostreams\Snippets;

class DefaultSnippets implements Snippets {

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
	public function file($file, $text=null, $class=null) {
		if (is_null($text)) {
			// if no text is given, we use the text originally provided by the user
			$text = $file;
		}
		$file = $this->getLink($file);
		$attributes = $this->getHtmlAttributes(array('class'=>$class));
		return "<a href='$file'$attributes>$text</a>";
	}

	/**
	 * Implements the 'youtube' snippet
	 */
	public function youtube($link, $width=480, $height=360) {
		$url = parse_url($link);
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





	/* internal functions */

	/**
	 * Determines whether or not a user-provided value evaluates to 'true'.
	 *
	 * This is the case whenever the provided value is "yes", "true" or "1".
	 *
	 * @param $value
	 * @return bool
	 */
	private function isTrue($value) {
		$value = strtolower(trim((string)$value));
		return (in_array($value, array("yes", "true", "1")));
	}

	/**
	 * Determines whether or not a user-provided value evaluates to 'false'.
	 * This is the case when 'isTrue()' does NOT evaluate to 'true'.
	 *
	 * @param $value
	 * @return bool
	 */
	private function isFalse($value) {
		return !$this->isTrue($value);
	}

	/**
	 * Turns internal links into proper absolute links, and/or adds 'http'
	 * to external links that were specified without one.
	 *
	 * @param $link
	 * @return string
	 */
	private function getLink($link) {
		$url_parts = parse_url($link);

		if (is_array($url_parts)) {
			if (!array_key_exists("scheme", $url_parts)) {
				// we have a partially incomplete URL here
				// -> see if we just have to add 'http://'
				//    or if we are dealing with an internal link
				$p = explode('/', $url_parts["path"]);
				$domain = $p[0];
				if ($this->isValidDomainName($domain)) {
					// the first part of the link is a valid domain name
					// -> just prepend 'http://' and continue
					return "http://$link";
				} else {
					// this is an internal link
					return \Phile\Utility::getBaseUrl() . '/'. ltrim($link, '/');
				}
			}
		}

		// PHP could not parse the link, which means it's probably invalid. However,
		// in this case, we just return whatever the user typed.
		return $link;
	}

	/**
	 * Checks if the given string is a valid domain name
	 * @param $domain_name
	 * @return bool
	 */
	private function isValidDomainName($domain_name)
	{
		return (preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $domain_name) //valid chars check
			&& preg_match('/^.{1,253}$/', $domain_name) //overall length check
			&& preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $domain_name)   ) //length of each label
			&& count(explode('.', $domain_name))>1;
	}


	/**
	 * Turns a list of attributes provided as an associative array into an equivalent string
	 * of HTML attributes that can be inserted into a HTML tag. Skips any attributes that
	 * are considered empty.
	 *
	 * @param $attributes
	 * @return string
	 */
	private function getHtmlAttributes($attributes) {
		$list = array();
		foreach ($attributes as $name=>$value) {
			if (!empty($value)) {
				$list[] = "$name='" . htmlentities($value, ENT_QUOTES, "UTF-8") . "'";
			}
		}
		if (count($list)>0) {
			return " " . implode(" ", $list);
		}

		return "";
	}
}