import { Navigate, Outlet, useLocation } from 'react-router-dom';
import { useAuth } from '@/features/auth/AuthProvider';
import type { ApiRole } from '@/shared/types/auth';

function FullScreenLoader() {
  return (
    <div className="flex min-h-screen items-center justify-center bg-slate-50">
      <div className="rounded-xl border border-slate-200 bg-white px-6 py-4 text-sm text-slate-600">
        Cargando sesion...
      </div>
    </div>
  );
}

export function RequireAuth({ role }: { role?: ApiRole }) {
  const { session, loading } = useAuth();
  const location = useLocation();

  if (loading) {
    return <FullScreenLoader />;
  }

  if (!session) {
    return <Navigate to="/login" replace state={{ from: location }} />;
  }

  if (role && session.role !== role) {
    return <Navigate to={session.role === 'student' ? '/app' : '/admin'} replace />;
  }

  return <Outlet />;
}

export function RequireEmployeePermission({ ability }: { ability: string }) {
  const { hasPermission } = useAuth();

  if (!hasPermission(ability)) {
    return (
      <div className="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800">
        No tienes permiso para acceder a este modulo.
      </div>
    );
  }

  return <Outlet />;
}
