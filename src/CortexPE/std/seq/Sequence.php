<?php


namespace CortexPE\std\seq;


use Generator;

final class Sequence {
	private function __construct() {
	}

	/**
	 * @param array $old
	 * @param array $new
	 * @return Generator<SequentialDifference>
	 */
	public static function difference(array $old, array $new): Generator {
		foreach($old as $k => $v) {
			foreach($new as $j => $i) {
				if($k === $j && $v === $i) break;
				if($v !== $i) continue;
				yield new SequentialDifference($k, $j, $i);
				break;
			}
		}
	}
}