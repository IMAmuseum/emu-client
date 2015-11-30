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

class IMuXMLDocument extends IMuDocument
{
	public function
	__construct($name = null, $public = null, $system = null)
	{
		parent::__construct($name, $public, $system);

		$this->_dom->formatOutput = true;
		$this->_options = array();
	}

	public function
	element($name, $value = '')
	{
		$elem = parent::element($name);
		$elem->push();
		if (is_array($value))
		{
			if (array_keys($value) === range(0, count($value) - 1))
				$this->writeList($name, $value);
			else
				$this->writeHash($name, $value);
		}
		else if (is_object($value))
			$this->writeObject($name, $value);
		else
			$this->writeText($name, $value);
		$elem->pop();
		return $elem;
	}

	public function
	getTagOption($tag, $name, $default = false)
	{
		if (! array_key_exists($tag, $this->_options))
			return $default;
		if (! array_key_exists($name, $this->_options[$tag]))
			return $default;
		return $this->_options[$tag][$name];
	}

	public function
	hasTagOption($tag, $name)
	{
		if (! array_key_exists($tag, $this->_options))
			return false;
		return array_key_exists($name, $this->_options[$tag]);
	}

	public function
	setTagOption($tag, $name, $value)
	{
		if (! array_key_exists($tag, $this->_options))
			$this->_options[$tag] = array();
		$this->_options[$tag][$name] = $value;
	}

	protected $_options;

	protected function
	writeList($tag, $list)
	{
		/* This is an ugly hack */
		if ($this->hasTagOption($tag, 'child'))
			$child = $this->getTagOption($tag, 'child');
		else if (preg_match('/(.*)s$/', $tag, $match))
			$child = $match[1];
		else if (preg_match('/(.*)_tab$/', $tag, $match))
			$child = $match[1];
		else if (preg_match('/(.*)0$/', $tag, $match))
			$child = $match[1];
		else if (preg_match('/(.*)_nesttab$/', $tag, $match))
			$child = $match[1] . '_tab';
		else
			$child = 'item';

		foreach ($list as $item)
			$this->element($child, $item);
	}

	protected function
	writeHash($tag, $hash)
	{
		foreach ($hash as $name => $value)
			$this->element($name, $value);
	}

	protected function
	writeObject($tag, $object)
	{
		foreach (get_object_vars($object) as $name => $value)
			$this->element($name, $value);
	}

	protected function
	writeText($tag, $text)
	{
		if ($text !== '')
		{
			$type = gettype($text);
			if ($type == 'boolean')
				$text = $text ? 'true' : 'false';

			/* Check if special processing is required
			*/
			if ($this->getTagOption($tag, 'html', false))
				$this->writeHTML($text);
			else if ($this->getTagOption($tag, 'xml', false))
				$this->writeXML($text);
			/* Deprecated: use 'xml' option instead */
			else if ($this->getTagOption($tag, 'raw', false))
				$this->writeXML($text);
			else
				$this->top()->text($text);
		}
	}

	protected function
	writeHTML($text)
	{
		/* Transform entities as these break the XML processing
		*/
		$text = preg_replace('/&nbsp;/', '&#160;', $text);
		// TODO other transformations

		return $this->writeXML($text);
	}

	protected function
	writeXML($text)
	{
		$this->top()->fragment($text);
	}
}
?>
