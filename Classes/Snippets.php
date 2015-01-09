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
		$page = new \Phile\Repository\Page();
		$linked_page = $page->findByPath($link);

		if ($linked_page) {
			// the user linked to an internal page
			return \Phile\Utility::getBaseUrl() . '/'. $linked_page->getUrl();
		}

		$file = ROOT_DIR . DIRECTORY_SEPARATOR . $link;
		if (file_exists($file)) {
			// the user linked to an internal file
			return \Phile\Utility::getBaseUrl() . '/' . $link;
		}

		// it's not an internal page, it's not an internal file -
		// let's see if it's (at least) a somewhat valid URL
		$url_parts = parse_url($link);

		if (is_array($url_parts)) {
			if (!array_key_exists("scheme", $url_parts)) {
				// it doesn't have a http:// or https:// or similar prefix.
				// This could mean the user provided something like: (link: cnn.com)
				// -> check if the first part of the link looks like a domain name
				$p = explode('/', $url_parts["path"]);
				$domain = $p[0];

				if ($this->isValidDomainName($domain)) {
					// the first part of the link looks like a valid domain name
					// -> just prepend 'http://' and continue
					return "http://$link";
				}
			}
		}

		// If we get to this point, we just return whatever the user typed.
		return $link;
	}

	/**
	 * Checks if the given string looks like a valid domain name
	 * @param $domain_name
	 * @return bool
	 */
	protected function isValidDomainName($domain_name) {
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
	 * For example uses, please see the DefaultSnippets class.
	 *
	 * @param $attributes
	 * @return string
	 */
	protected function getHtmlAttributes($attributes) {
		$list = array();
		foreach ($attributes as $name=>$value) {
			if (!empty($value)) {
				if (is_scalar($value)) {
					$list[] = "$name='" . htmlentities($value, ENT_QUOTES, "UTF-8") . "'";
				} elseif (is_array($value)) {
					$list[] = $this->getHtmlAttributes($value);
				}
			}
		}
		if (count($list)>0) {
			return " " . implode(" ", $list);
		}

		return "";
	}
}
