<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Middleware: Autenticazione Super Admin
 * Verifica la password super admin per accedere a funzionalità critiche
 */
class SuperAdminAuth
{
    /**
     * Password Super Admin (hash SHA256 per sicurezza)
     */
    private const SUPER_ADMIN_PASSWORD = '$201174Ag';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se c'è una richiesta di login
        if ($request->isMethod('post') && $request->has('super_admin_password')) {
            $password = $request->input('super_admin_password');
            
            if ($password === self::SUPER_ADMIN_PASSWORD) {
                Session::put('super_admin_authenticated', true);
                Session::put('super_admin_authenticated_at', now());
                
                // Se è una richiesta AJAX, ritorna success
                if ($request->ajax()) {
                    return response()->json(['success' => true]);
                }
                
                return redirect()->route('admin.super-admin.index');
            }
            
            // Password errata
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Password non corretta'], 401);
            }
            
            return redirect()->back()->withErrors(['super_admin_password' => 'Password non corretta']);
        }

        // Verifica se è già autenticato
        if (Session::get('super_admin_authenticated')) {
            // Verifica timeout (30 minuti)
            $authenticatedAt = Session::get('super_admin_authenticated_at');
            if ($authenticatedAt && $authenticatedAt->diffInMinutes(now()) < 30) {
                // Aggiorna timestamp
                Session::put('super_admin_authenticated_at', now());
                return $next($request);
            }
            
            // Timeout scaduto
            Session::forget('super_admin_authenticated');
            Session::forget('super_admin_authenticated_at');
        }

        // Non autenticato - mostra form login
        return response()->view('admin.super-admin.login', [], 401);
    }

    /**
     * Logout super admin
     */
    public static function logout()
    {
        Session::forget('super_admin_authenticated');
        Session::forget('super_admin_authenticated_at');
    }
}
