import { NavLink, Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from '@/features/auth/AuthProvider';
import { Button } from '@/shared/ui/Button';
import { BrandLogo } from '@/shared/ui/BrandLogo';

export function AdminLayout() {
  const { session, logout, hasPermission } = useAuth();
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-slate-100">
      <header className="border-b border-brand-100 bg-white">
        <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3">
          <div className="flex items-center gap-3">
            <BrandLogo className="h-10" />
            <div>
              <p className="text-xs uppercase tracking-wide text-slate-500">Panel Administrativo</p>
              <p className="text-sm font-semibold text-slate-800">
                {session?.user.first_name} {session?.user.surnames}
              </p>
            </div>
          </div>

          <div className="flex items-center gap-2 text-sm">
            <NavLink to="/admin">Dashboard</NavLink>
            <NavLink to="/admin/prospects">Prospectos</NavLink>
            <NavLink to="/admin/cohorts">Cohortes</NavLink>
            <NavLink to="/admin/calendars">Calendarios</NavLink>
            <NavLink to="/admin/payments">Pagos</NavLink>
            <NavLink to="/admin/students">Estudiantes</NavLink>
            {hasPermission('manage_employees') && <NavLink to="/admin/employees">Empleados</NavLink>}
            <Button
              variant="secondary"
              onClick={async () => {
                await logout();
                navigate('/login', { replace: true });
              }}
            >
              Salir
            </Button>
          </div>
        </div>
      </header>

      <main className="mx-auto max-w-7xl px-4 py-6">
        <Outlet />
      </main>
    </div>
  );
}
