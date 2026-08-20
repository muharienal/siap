<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            $response = [
                'success' => false,
                'message' => 'You do not have the required authorization.',
            ];

            return response()->json($response, 403);
        });

        // FIX: sebelumnya sesi/token CSRF yang sudah kedaluwarsa (mis. form
        // login dibiarkan terbuka lama sebelum disubmit, atau ada tab lama
        // yang di-reload) menampilkan halaman error 419 mentah bawaan
        // Laravel — bikin bingung ("kok pas login malah error?"). Sekarang
        // diarahkan balik ke halaman sebelumnya (biasanya form login) dengan
        // pesan yang jelas, dan tidak dianggap error tak tertangani untuk
        // request yang memang mengharapkan JSON (mis. polling live-update).
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir.',
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation', 'current_password']))
                ->with('error', 'Sesi Anda telah berakhir (terlalu lama tidak aktif). Silakan coba lagi.');
        });
    }
}
