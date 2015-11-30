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

class IMuConfig
{
	public function
	__construct()
	{
		$this->values = null;
	}

	public $values;

	public function
	load($file)
	{
		$doc = new DOMDocument;
		if (! @$doc->load($file))
			throw new IMuException('ConfigLoad', $file);
		$xpath = new DOMXpath($doc);
		$values = $this->loadNode($xpath, $doc->documentElement);
		$this->mergeValue($this->values, $values);
	}

	public function
	merge($values)
	{
		$this->mergeValue($this, $values);
	}

	protected function
	loadNode($xpath, $node)
	{
		$children = $xpath->query('*', $node);
		$length = $children->length;
		if ($length == 0)
			return $node->nodeValue;
		if ($length == 1)
		{
			$type = $node->getAttribute('type');
			if ($type == '')
				$type = 'set';
		}
		else
		{
			$type = 'list';
			$first = $children->item(0)->nodeName;
			for ($i = 1; $i < $length; $i++)
			{
				$child = $children->item($i);
				if ($child->nodeName != $first)
				{
					$type = 'set';
					break;
				}
			}
		}
		if ($type == 'list')
		{
			$list = array();
			for ($i = 0; $i < $length; $i++)
			{
				$child = $children->item($i);
				$list[] = $this->loadNode($xpath, $child);
			}
			return $list;
		}
		$set = array();
		for ($i = 0; $i < $length; $i++)
		{
			$child = $children->item($i);
			$name = $child->nodeName;
			$set[$name] = $this->loadNode($xpath, $child);
		}
		return $set;
	}

	protected function
	mergeValue(&$old, $new)
	{
		if (! is_array($old))
			$old = $new;
		else if (array_keys($old) === range(0, count($old) - 1))
		{
			/* list */
			if (is_array($new))
			{
				foreach ($new as $name => $value)
					$old[] = $value;
			}
			else
				$old[] = $new;
		}
		else
		{
			/* set */
			if (is_array($new))
			{
				foreach ($new as $name => $value)
				{
					if (array_key_exists($name, $old))
						$this->mergeValue($old[$name], $value);
					else
						$old[$name] = $value;
				}
			}
		}
	}
}
?>
