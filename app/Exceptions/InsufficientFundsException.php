<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilemparkan apabila baki tidak mencukupi untuk sesuatu operasi.
 */
class InsufficientFundsException extends RuntimeException
{
}