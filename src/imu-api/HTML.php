<?php
/* KE Software Open Source Licence
** 
** Notice: Copyright (c) 2011-2013 KE SOFTWARE PTY LTD (ACN 006 213 298)
** (the "Owner"). All rights reserved.
** 
** Licence: Permission is hereby granted, free of charge, to any person
** obtaining a copy of this software and associated documentation files
** (the "Software"), to deal with the Software without restriction,
** including without limitation the rights to use, copy, modify, merge,
** publish, distribute, sublicense, and/or sell copies of the Software,
** and to permit persons to whom the Software is furnished to do so,
** subject to the following conditions.
** 
** Conditions: The Software is licensed on condition that:
** 
** (1) Redistributions of source code must retain the above Notice,
**     these Conditions and the following Limitations.
** 
** (2) Redistributions in binary form must reproduce the above Notice,
**     these Conditions and the following Limitations in the
**     documentation and/or other materials provided with the distribution.
** 
** (3) Neither the names of the Owner, nor the names of its contributors
**     may be used to endorse or promote products derived from this
**     Software without specific prior written permission.
** 
** Limitations: Any person exercising any of the permissions in the
** relevant licence will be taken to have accepted the following as
** legally binding terms severally with the Owner and any other
** copyright owners (collectively "Participants"):
** 
** TO THE EXTENT PERMITTED BY LAW, THE SOFTWARE IS PROVIDED "AS IS",
** WITHOUT ANY REPRESENTATION, WARRANTY OR CONDITION OF ANY KIND, EXPRESS
** OR IMPLIED, INCLUDING (WITHOUT LIMITATION) AS TO MERCHANTABILITY,
** FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. TO THE EXTENT
** PERMITTED BY LAW, IN NO EVENT SHALL ANY PARTICIPANT BE LIABLE FOR ANY
** CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
** TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
** SOFTWARE OR THE USE OR OTHER DEALINGS WITH THE SOFTWARE.
** 
** WHERE BY LAW A LIABILITY (ON ANY BASIS) OF ANY PARTICIPANT IN RELATION
** TO THE SOFTWARE CANNOT BE EXCLUDED, THEN TO THE EXTENT PERMITTED BY
** LAW THAT LIABILITY IS LIMITED AT THE OPTION OF THE PARTICIPANT TO THE
** REPLACEMENT, REPAIR OR RESUPPLY OF THE RELEVANT GOODS OR SERVICES
** (INCLUDING BUT NOT LIMITED TO SOFTWARE) OR THE PAYMENT OF THE COST OF SAME.
*/
require_once dirname(__FILE__) . '/IMu.php';
require_once IMu::$api . '/Document.php';
require_once IMu::$api . '/Trace.php';

class IMuHTMLDocument extends IMuDocument
{
	public function
	__construct($public = 'strict', $url = '')
	{
		if ($public == 'strict')
		{
			$public = '-//W3C//DTD HTML 4.01//EN';
			$url = 'http://www.w3.org/TR/html4/strict.dtd';
		}
		else if ($public == 'transitional')
		{
			$public = '-//W3C//DTD HTML 4.01 Transitional//EN';
			$url = 'http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd';
		}
		parent::__construct('html', $public, $url);

		$this->elementClass('IMuHTMLElement');
	}

	public function
	output()
	{
		header('Content-type: text/html');
		print $this->saveHTML();
	}

	public function
	newDocumentFragment($fragment, $parent)
	{
		/* Load the fragment by creating a separate HTML document and
		** extracting the nodes from that.
		**
		** The fragment is embedded in a UTF-8 compliant HTML document.
		** This is as described in various user comments at
		** http://www.php.net/manual/en/domdocument.loadhtml.php
		*/
		$html = <<<EOF
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
	</head>
	<body>
		$fragment
	</body>
</html>
EOF;

		$dom = new DOMDocument();
		if (! $dom->loadHTML($html))
		{
			IMuTrace(3, 'newDocumentFragment: loadHTML failed');
			return;
		}
		$list = $dom->getElementsByTagName('body');
		$body = $list->item(0);
		$list = $body->childNodes;
		$length = $list->length;
		for ($i = 0; $i < $length; $i++)
		{
			$node = $list->item($i);
			$node = $parent->ownerDocument->importNode($node, true);
			$parent->appendChild($node);
		}
	}

	/* Convenience */

	/* head elements */
	public function
	css($name, $media = null)
	{
		if (! preg_match('/\.css$/', $name))
			$name .= '.css';
		$node = $this->element('link', false);
		$node->attr('rel', 'stylesheet');
		$node->attr('type', 'text/css');
		$node->attr('href', $name);
		if ($media !== null)
			$node->attr('media', $media);
		return $node;
	}

	public function
	js($name)
	{
		if (! preg_match('/\.js$/', $name))
			$name .= '.js';
		$node = $this->element('script', false);
		$node->attr('type', 'text/javascript');
		$node->attr('src', $name);
		return $node;
	}

	public function
	title($text)
	{
		$node = $this->element('title', false);
		$node->text($text);
		return $node;
	}
}

class IMuHTMLElement extends IMuDocumentElement
{
	public function
	addClass($value)
	{
		$class = $this->getClassArray();
		$index = array_search($value, $class);
		if ($index === false)
		{
			$class[] = $value;
			$this->attr('class', implode(' ', $class));
		}
		return $this->attr('class');
	}

	public function
	css($name, $value = null)
	{
		$styles = $this->attr('style');
		if ($styles == '')
			$styles = array();
		else
			$styles = preg_split('/;\s*/', $styles);

		$entries = array();
		foreach ($styles as $style)
		{
			if (! preg_match('/^([^:]*):(.*)$/', $style, $m))
				continue;
			$styleName = $m[1];
			$styleValue = $m[2];

			$styleName = preg_replace('/^\s+/', '', $styleName);
			$styleName = preg_replace('/\s+$/', '', $styleName);

			$styleValue = preg_replace('/^\s+/', '', $styleValue);
			$styleValue = preg_replace('/\s+$/', '', $styleValue);

			$entries[] = array($styleName, $styleValue);
		}

		$index = -1;
		for ($i = 0; $i < count($entries); $i++)
		{
			if ($entries[$i][0] == $name)
			{
				$index = $i;
				break;
			}
		}
		if ($value === null)
		{
			if ($index < 0)
				return '';
			return $entries[$index][1];
		}

		if ($index < 0)
			$entries[] = array($name, $value);
		else
			$entries[$index][1] = $value;

		$style = '';
		for ($i = 0; $i < count($entries); $i++)
		{
			if ($i > 0)
				$style .= ' ';
			$style .= $entries[$i][0] . ': ' . $entries[$i][1] . ';';
		}
		$this->attr('style', $style);
	}

	public function
	hasClass($value)
	{
		$class = $this->getClassArray();
		return array_search($value, $class) !== false;
	}

	public function
	id($value = null)
	{
		if ($value !== null)
			$this->attr('id', $value);
		return $this->attr('id');
	}

	public function
	removeClass($value)
	{
		$class = $this->getClassArray();
		$index = array_search($value, $class);
		if ($index !== false)
		{
			array_splice($class, $index, 1);
			$this->attr('class', implode(' ', $class));
		}
		return $this->attr('class');
	}

	protected function
	getClassArray()
	{
		$class = $this->attr('class');
		if ($class == '')
			return array();
		return preg_split('/\s+/', $class);
	}
}
?>
