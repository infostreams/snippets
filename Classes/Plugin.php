<?php
namespace Phile\Plugin\Infostreams\Snippets;

class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	protected $snippets = array();

	public function __construct() {
		\Phile\Event::registerEvent('before_parse_content', $this);
	}

	public function on($eventKey, $data = null) {
		if ($eventKey == "before_parse_content") {
			$data['page']->setContent($this->parse($data['content']));
		}
	}

	/**
	 * Adds one snippet or a class of Snippets.
	 *
	 * @param string|Snippets $snippet
	 * @param Callable $callable
	 */
	public function add($snippet, Callable $callable=null) {
		if (is_object($snippet) && $snippet instanceof Snippets) {
			$this->addSnippetClass($snippet);
		}
		if (is_string($snippet) && is_callable($callable)) {
			// '$snippet' is a tag name: register $callable
			$this->snippets[$snippet] = $callable;
		}
	}

	/**
	 * inject settings
	 *
	 * @param array $settings
	 */
	public function injectSettings(array $settings = null) {
		$this->settings = ($settings === null) ? array() : $settings;

		// register default snippets
		$this->add(new DefaultSnippets());

		if (array_key_exists('snippets', $this->settings)) {
			foreach ($this->settings['snippets'] as $snippet=>$definition) {
				if ($definition instanceof Snippets) {
					$this->add($definition);
				} else {
					$this->add($snippet, $definition);
				}
			}
		}

	}

	/**
	 * @param null $snippet
	 * @return array|Callable|bool
	 */
	public function get($snippet=null) {
		if (!is_null($snippet)) {
			if (array_key_exists($snippet, $this->snippets)) {
				return $snippets[$snippet];
			}
			return false;
		}
		return $this->snippets;
	}

	protected function parse($content) {
		$snippets = $this->get();
		if (count($snippets)>0) {
			$tags = array_keys($snippets);

			$matches = array();
			$regexp = '#\((' . implode($tags, '|') . ')\:\s(.*?)\)#i';

			if ($count = preg_match_all($regexp, $content, $matches) > 0) {
				$tags = $matches[1];
				foreach ($tags as $i=>$tag) {
					$full_snippet = $matches[0][$i];
					$attributes = $matches[2][$i];

					$output = $this->render($snippets[$tag], $attributes);

					$content = str_replace($full_snippet, $output, $content);
				}
			}
		}

		return $content;
	}

	protected function render($callable, $attributes) {

		// extract parameter names from the provided function
		if (is_array($callable)) {
			$f = new \ReflectionMethod($callable[0], $callable[1]);
		} elseif (is_a($callable, 'Closure')) {
			$f = new \ReflectionFunction($callable);
		} else {
			// file?
			return "";
		}

		$function_params = $f->getParameters();
		$parameter_names = array_map(function($f) { return $f->name; }, $function_params);

		// first parameter is the one directly after the tag name,
		// which means we don't have to look for it
		$look_for = array_slice($parameter_names, 1);

		// extract parameter values from the provided attributes
		$regexp = '#(' . implode($look_for, '|') . '):#i';
		$matches = preg_split($regexp, $attributes, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		// collect them in a 'parameter name': 'parameter value' array
		array_unshift($matches, $parameter_names[0]);
		$params_unsorted = array();
		for ($i=0; $i<count($matches); $i+=2) {
			$params_unsorted[$matches[$i]] = $matches[$i+1];
		}

		// put them in the order as they are used in the function,
		// and provide sane values for any missing parameters
		$params_sorted = array();
		foreach ($function_params as $i=>$p) {
			if (array_key_exists($p->name, $params_unsorted)) {
				$params_sorted[$i] = trim($params_unsorted[$p->name]);
			} else {
				// php doesn't allow 'skipping' function parameters,
				// so if a value is not provided, try to get its default value
				if ($p->isDefaultValueAvailable()) {
					$params_sorted[$i] = $p->getDefaultValue();
				} else {
					// if no default value is available, we use 'NULL'
					$params_sorted[$i] = NULL;
				}
			}
		}

		// finally call the function with these parameters
		return call_user_func_array($callable, $params_sorted);
	}

	private function addSnippetClass(Snippets $snippet) {
		// '$snippet' is a class that implements the 'Snippets' interface
		// -> register all public, non-magic methods as a snippet
		$methods = get_class_methods($snippet);
		foreach ($methods as $m) {
			if (substr($m,0,2)!=='__') {
				$this->snippets[$m] = array($snippet, $m);
			}
		}
	}


}

?>