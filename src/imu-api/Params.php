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

class IMuParams
{
	const REPEATS_NEVER = 0;
	const REPEATS_AS_NEEDED = 1;
	const REPEATS_ALWAYS = 2;

	public function
	__construct($repeats = REPEATS_NEVER)
	{
		$this->repeats = $repeats;
		$this->list = array();

		/* GET */
		$this->load($_SERVER['QUERY_STRING']);

		/* POST */
		$this->load(file_get_contents('php://input'));
	}

	public $repeats;
	public $list;

	public function
	get($name, $value = null)
	{
		if (array_key_exists($name, $this->list))
			$value = $this->list[$name];
		return $value;
	}

	public function
	has($name)
	{
		return array_key_exists($name, $this->list);
	}

	public function
	remove($name, $value = null)
	{
		if (array_key_exists($name, $this->list))
		{
			$value = $this->list[$name];
			unset($this->list[$name]);
		}
		return $value;
	}

	private function
	load($string)
	{
		if ($string == '')
			return;

		foreach (explode('&', $string) as $pair)
		{
			$pair = explode('=', $pair);
			$name = urldecode($pair[0]);
			$value = urldecode($pair[1]);

			switch ($this->repeats)
			{
			  case self::REPEATS_AS_NEEDED:
				if (! array_key_exists($name, $this->list))
					$this->list[$name] = $value;
				else if (! is_array($this->list[$name]))
					$this->list[$name] = array($this->list[$name], $value);
				else
					$this->list[$name][] = $value;
				break;

			  case self::REPEATS_ALWAYS:
				$this->list[$name][] = $value;
				break;

			  case self::REPEATS_NEVER:
			  default:
				$this->list[$name] = $value;
				break;
			}
		}
	}
}
?>
