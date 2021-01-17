<?php
namespace App\Storage;


class Storage {
		public function save($name, $value) {
			request()->session()->put($name, $value);
		}
		
		public function load($name) {
				return request()->session()->get($name);
		}
		
		public function delete($name) {
			 request()->session()->forget($name);
		}
}