<?php
use think\facade\Route;

Route::get('OUR SOLUTIONS/:id', 'portal/Article/index?cid=1')->append(array('cid' => '1',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('ff/:id', 'job/Post/index?cid=5')->append(array('cid' => '5',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('xny/:id', 'job/Post/index?cid=6')->append(array('cid' => '6',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('cbb/:id', 'job/Post/index?cid=7')->append(array('cid' => '7',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('rjg/:id', 'job/Post/index?cid=8')->append(array('cid' => '8',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('bdgz/:id', 'job/Post/index?cid=9')->append(array('cid' => '9',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('baas/:id', 'portal/Article/index?cid=2')->append(array('cid' => '2',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('funds-services/:id', 'portal/Article/index?cid=3')->append(array('cid' => '3',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('strategy-consulting/:id', 'portal/Article/index?cid=4')->append(array('cid' => '4',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('concierge-services/:id', 'portal/Article/index?cid=5')->append(array('cid' => '5',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('serviced-office/:id', 'portal/Article/index?cid=6')->append(array('cid' => '6',))
->pattern(array('id' => '\d+',  'cid' => '\d+',));

Route::get('OUR SOLUTIONS', 'portal/List/index?id=1')->append(array('id' => '1',))
->pattern(array('id' => '\d+',));

Route::get('ff', 'job/List/index?id=5')->append(array('id' => '5',))
->pattern(array('id' => '\d+',));

Route::get('xny', 'job/List/index?id=6')->append(array('id' => '6',))
->pattern(array('id' => '\d+',));

Route::get('cbb', 'job/List/index?id=7')->append(array('id' => '7',))
->pattern(array('id' => '\d+',));

Route::get('rjg', 'job/List/index?id=8')->append(array('id' => '8',))
->pattern(array('id' => '\d+',));

Route::get('bdgz', 'job/List/index?id=9')->append(array('id' => '9',))
->pattern(array('id' => '\d+',));

Route::get('baas', 'portal/List/index?id=2')->append(array('id' => '2',))
->pattern(array('id' => '\d+',));

Route::get('funds-services', 'portal/List/index?id=3')->append(array('id' => '3',))
->pattern(array('id' => '\d+',));

Route::get('strategy-consulting', 'portal/List/index?id=4')->append(array('id' => '4',))
->pattern(array('id' => '\d+',));

Route::get('concierge-services', 'portal/List/index?id=5')->append(array('id' => '5',))
->pattern(array('id' => '\d+',));

Route::get('serviced-office', 'portal/List/index?id=6')->append(array('id' => '6',))
->pattern(array('id' => '\d+',));

Route::get('contact', 'portal/Index/contact')
->pattern(array('id' => '\d+',));


