import { Card } from '@/shared/ui/Card';

export function AdminDashboardPage() {
  return (
    <div className="grid gap-4 md:grid-cols-3">
      <Card>
        <p className="text-xs uppercase text-slate-500">Operacion</p>
        <p className="mt-2 text-lg font-bold text-slate-900">Prospectos / Estudiantes</p>
      </Card>
      <Card>
        <p className="text-xs uppercase text-slate-500">Academico</p>
        <p className="mt-2 text-lg font-bold text-slate-900">Cohortes / Calendarios</p>
      </Card>
      <Card>
        <p className="text-xs uppercase text-slate-500">Finanzas</p>
        <p className="mt-2 text-lg font-bold text-slate-900">Pagos y conciliacion</p>
      </Card>
    </div>
  );
}
