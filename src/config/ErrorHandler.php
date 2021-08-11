<?php
use Urabe\Config\UrabeSettings;
use Urabe\Config\ConnectionError;
/**
 * Handles application errors
 *
 * @param int $err_no Contains the level of the error raised, as an integer. 
 * @param string $err_msg The error message, as a string. 
 * @param string  $err_file The filename that the error was raised in, as a string
 * @param int $err_line The line number the error was raised at, as an integer
 * @param array $err_context an array that points to the active symbol table at the point the error occurred. 
 * In other words, err_context will contain an array of every variable that existed in the scope the error was triggered in. 
 * User error handler must not modify error context. 
 * @return bool Returns a string containing the previously defined error handler.
 */
function error_handler($err_no, $err_msg, $err_file, $err_line, $err_context)
{
    $error = new ConnectionError();
    $error->code = $err_no;
    $error->message = $err_msg;
    $error->file = $err_file;
    $error->line = $err_line;
    $error->set_err_context($err_context);
    array_push(UrabeSettings::$errors, $error);
}
/**
 * Handles application exceptions
 *
 * @param exception $exception The generated exception
 * @return void
 */
function exception_handler($exception)
{
    if (is_null(UrabeSettings::$http_error_code))
        http_response_code(400);
    else
        http_response_code(UrabeSettings::$http_error_code);
    $class = get_class($exception);
    $error = new ConnectionError();
    $error->code = $exception->getCode();
    $error->message = $exception->getMessage();
    $error->file = $exception->getFile();
    $error->line = $exception->getLine();
    if ($class == CLASS_SQL_EXC)
        $error->sql = $exception->sql;
    $response = new UrabeResponse();
    $response->error = $error->get_exception_error();
    $err = $response->get_exception_response(
        $exception->getMessage(),
        UrabeSettings::$enable_stack_trace ? $exception->getTraceAsString() : null
    );

    $exc_response = $response->get_exception_response(
        $exception->getMessage(),
        UrabeSettings::$enable_stack_trace ? $exception->getTraceAsString() : null
    );
    //If encoding fails means error context has resource objects that can not be encoded,
    //in that case will try the simple exception response
    $sql = $exc_response->error[NODE_QUERY];
    $exc_response = json_encode($exc_response);

    if (!$exc_response) {
        $exc_response = $response->get_simple_exception_response(
            $exception,
            UrabeSettings::$enable_stack_trace ? $exception->getTraceAsString() : null
        );
        if (UrabeSettings::$add_query_to_response)
            $exc_response->{NODE_SQL} = $sql;
        $exc_response->{NODE_SUCCEED} = false;
        $exc_response = json_encode($exc_response);
    }
    echo $exc_response;
}
/****************************
 * Inicialización de manejo *
 *        de errores        *
 * **************************/
if (UrabeSettings::$handle_errors)
    set_error_handler('error_handler');
if (UrabeSettings::$handle_errors)
    set_exception_handler('exception_handler');
