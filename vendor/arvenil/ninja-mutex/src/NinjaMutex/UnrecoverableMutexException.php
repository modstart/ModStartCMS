<?php
/**
 * This file is part of ninja-mutex.
 *
 * (C) Kamil Dziedzic <arvenil@klecza.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NinjaMutex;

/**
 * Unrecoverable Mutex exception
 *
 * You shouldn't try to catch it unless you really know what are you doing
 * This kind of exception suggest you messed up your code and you should fix it
 *
 * @author Kamil Dziedzic <arvenil@klecza.pl>
 */
class UnrecoverableMutexException extends MutexException
{
}
