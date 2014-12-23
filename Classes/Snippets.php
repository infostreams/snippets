<?php
namespace Phile\Plugin\Infostreams\Snippets;

class Snippets {
	/**
	 * Determines whether or not a user-provided value evaluates to 'true'.
	 *
	 * This is the case whenever the provided value is "yes", "true" or "1".
	 *
	 * @param $value
	 * @return bool
	 */
	protected function isTrue($value) {
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
	protected function isFalse($value) {
		return !$this->isTrue($value);
	}

	/**
	 * Turns internal links into proper absolute links, and/or adds 'http'
	 * to external links that were specified without one.
	 *
	 * @param $link
	 * @return string
	 */
	protected function getLink($link) {
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
	protected function isValidDomainName($domain_name)
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
	protected function getHtmlAttributes($attributes) {
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
