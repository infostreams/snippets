<?php
namespace Phile\Plugin\Infostreams\Snippets;

/**
 * Parses Snippet attribute values, be it strings, numbers, arrays, or associative arrays, and
 * converts them into PHP variables.
 *
 * Examples:
 *
 * <code>
 * (youtube: https://www.youtube.com/watch?v=mSB71jNq-yQ)
 * </code>
 *
 * Here, SnippetValue will be called on the YouTube URL, and produce the string
 * "https://www.youtube.com/watch?v=mSB71jNq-yQ".
 *
 * <code>
 * (
 *
 * Class SnippetValue
 * @package Phile\Plugin\Infostreams\Snippets
 */
class SnippetValue {
	const TOKEN_END           = 0;
	const TOKEN_BRACKET_OPEN  = 1;
	const TOKEN_BRACKET_CLOSE = 2;
	const TOKEN_CURLY_OPEN    = 3;
	const TOKEN_CURLY_CLOSE   = 4;
	const TOKEN_COMMA         = 5;
	const TOKEN_COLON         = 6;
	const TOKEN_SCALAR        = 7;

	public static function parse($value) {
		$obj = new self();
		return $obj->parseValue($value);
	}

	protected function parseValue($value, &$index=0, &$success=true, &$array_depth=0) {
		switch ($this->lookAhead($value, $index)) {
			case self::TOKEN_BRACKET_OPEN:
			case self::TOKEN_CURLY_OPEN:
				$array_depth++;
				return $this->parseArray($value, $index, $success, $array_depth);

			case self::TOKEN_SCALAR:
				return $this->parseScalar($value, $index, $success, $array_depth);

			case self::TOKEN_END:
				break;
		}

		$success=false;
		return null;
	}

	protected function lookAhead($value, &$index) {
		$copy = 0 + $index;
		return $this->nextToken($value, $copy);
	}

	protected function nextToken($value, &$index) {
		$this->eatWhitespace($value, $index);

		if ($index >= strlen($value)) {
			return self::TOKEN_END;
		}

		$c = $value[$index];
		$index++;
		switch ($c) {
			case '[':
				return self::TOKEN_BRACKET_OPEN;
			case ']':
				return self::TOKEN_BRACKET_CLOSE;
			case '{':
				return self::TOKEN_CURLY_OPEN;
			case '}':
				return self::TOKEN_CURLY_CLOSE;
			case ',':
				return self::TOKEN_COMMA;
			case ':':
				return self::TOKEN_COLON;
			default:
				return self::TOKEN_SCALAR;
		}
	}

	protected function eatWhitespace($value, &$index) {
		while ($index<strlen($value)-1 && trim($value[$index])=='') {
			$index++;
		}
	}

	protected function parseArray($value, &$index, &$success, &$array_depth) {
		$array = array();

		// skip over opening bracket, i.e. '[' or '{'
		$this->nextToken($value, $index);

		$done = false;
		while (!$done) {
			$next_token = $this->lookAhead($value, $index);
			switch ($next_token) {
				case self::TOKEN_END:
					return $this->fail($success);

				case self::TOKEN_COMMA:
					// skip over comma, continue to next char
					$this->nextToken($value, $index);
					break;

				case self::TOKEN_CURLY_CLOSE:
				case self::TOKEN_BRACKET_CLOSE:
					// skip over closing bracket, return result
					$array_depth--;
					$this->nextToken($value, $index);
					break 2;

				default:
					$x = $this->parseValue($value, $index, $success, $array_depth);
					if (is_null($x)) {
						return null;
					}

					// see if this is an associative array
					if (is_scalar($x)) {
						if ($this->lookAhead($value, $index) == self::TOKEN_COLON) {
							// this array field has a key and a colon

							// skip the colon
							$this->nextToken($value, $index);

							// and parse what comes next
							$v = $this->parseValue($value, $index, $success, $array_depth);
							$array[$x] = $v;
							break;
						}
					}
					$array[] = $x;
			}
		}

		return $array;
	}

	protected function parseScalar($value, &$index, &$success, &$array_depth) {
		$this->eatWhitespace($value, $index);

		$is_quoted = $value[$index]=='\'' || $value[$index]=='"';
		$quote_char = null;

		$terminate_characters = array();
		if ($is_quoted) {
			$quote_char = $value[$index];
			$terminate_characters = array($quote_char);

			// skip quote char
			$this->nextToken($value, $index);
		} else {
			// the value is not quoted
			if ($array_depth>0) {
				// we are inside an array - terminate on the following non-escaped characters: ,:[]{}
				$terminate_characters = array(',', ':', '[', ']', '{', '}');
			} else {
				// we are *not* inside an array - terminate on ',' only
				$terminate_characters = array(',');
			}
		}

		$escape_char = '\\';
		$is_escaped = false;
		$scalar = "";
		while ($index<strlen($value)) {
			$c = $value[$index];

			if (in_array($c, $terminate_characters) && !$is_escaped) {
				if ($is_quoted) {
					$this->nextToken($value, $index);
				}
				return $scalar;
			}
			if ($c==$escape_char) {
				$is_escaped = true;
				$this->nextToken($value, $index);
				continue;
			}
			$scalar .= $c;

			$is_escaped = false;
			$index++;
		}

		return $scalar;
	}

	protected function fail(&$success) {
		$success = null;
		return false;
	}
}