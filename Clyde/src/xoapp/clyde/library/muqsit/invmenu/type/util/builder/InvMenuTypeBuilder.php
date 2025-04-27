<?php

declare(strict_types=1);

namespace xoapp\clyde\library\muqsit\invmenu\type\util\builder;

use xoapp\clyde\library\muqsit\invmenu\type\InvMenuType;

interface InvMenuTypeBuilder{

	public function build() : InvMenuType;
}