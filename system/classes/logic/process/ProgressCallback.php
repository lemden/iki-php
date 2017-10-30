<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\process;

interface ProgressCallback
{
    function trigger($percent);
}