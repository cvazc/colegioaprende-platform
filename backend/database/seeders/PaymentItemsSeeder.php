<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentItemsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $items = [
            // Preparatoria - Estudiante General
            $this->item('inscripcion', 'Inscripción', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 1390.00, 0, false, true, false, 10),
            $this->item('cuestionario', 'Cuestionario (Materia)', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 425.00, 1, false, true, false, 20),
            $this->item('registro', 'Registro', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 1630.00, 0, false, true, false, 30),
            $this->item('material', 'Material Didáctico', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 3249.00, 0, false, true, false, 40),
            $this->item('garantia', 'Garantía (Inscripción)', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 1390.00, 0, true, true, false, 50),
            $this->item('cuestionario_final', 'Cuestionario Final', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 5230.00, 0, false, true, false, 60),
            $this->item('certificacion', 'Certificación', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 3190.00, 0, false, true, false, 70),
            $this->item('total', 'Pago Total', 'Preparatoria 4 meses 16 sesiones', 'Estudiante General', 22879.00, 16, true, true, true, 80),

            // Preparatoria - Estudiante Preferencial
            $this->item('inscripcion', 'Inscripción', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 0.00, 0, false, false, false, 10),
            $this->item('cuestionario', 'Cuestionario (Materia)', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 250.00, 1, false, true, false, 20),
            $this->item('registro', 'Registro', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 0.00, 0, false, false, false, 30),
            $this->item('material', 'Material Didáctico', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 0.00, 0, false, false, false, 40),
            $this->item('garantia', 'Garantía (Inscripción)', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 690.00, 0, true, true, false, 50),
            $this->item('cuestionario_final', 'Cuestionario Final', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 4490.00, 0, false, true, false, 60),
            $this->item('certificacion', 'Certificación', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 2390.00, 0, false, true, false, 70),
            $this->item('total', 'Pago Total', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Preferencial', 11570.00, 16, true, true, true, 80),

            // Preparatoria - Estudiante Exclusivo
            $this->item('inscripcion', 'Inscripción', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 10),
            $this->item('cuestionario', 'Cuestionario (Materia)', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 1, false, false, false, 20),
            $this->item('registro', 'Registro', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 30),
            $this->item('material', 'Material Didáctico', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 40),
            $this->item('garantia', 'Garantía (Inscripción)', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 50),
            $this->item('cuestionario_final', 'Cuestionario Final', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 60),
            $this->item('certificacion', 'Certificación', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 70),
            $this->item('total', 'Pago Total', 'Preparatoria 4 meses 16 sesiones', 'Estudiante Exclusivo', 9997.00, 16, true, true, true, 80),

            // Secundaria - Estudiante General
            $this->item('inscripcion', 'Inscripción', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 1390.00, 0, false, true, false, 10),
            $this->item('cuestionario', 'Cuestionario (Materia)', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 425.00, 1, false, true, false, 20),
            $this->item('registro', 'Registro', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 1630.00, 0, false, true, false, 30),
            $this->item('material', 'Material Didáctico', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 3249.00, 0, false, true, false, 40),
            $this->item('garantia', 'Garantía (Inscripción)', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 1390.00, 0, true, true, false, 50),
            $this->item('cuestionario_final', 'Cuestionario Final', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 4230.00, 0, false, true, false, 60),
            $this->item('certificacion', 'Certificación', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 3190.00, 0, false, true, false, 70),
            $this->item('total', 'Pago Total', 'Secundaria 4 meses 16 sesiones', 'Estudiante General', 21879.00, 16, true, true, true, 80),

            // Secundaria - Estudiante Preferencial
            $this->item('inscripcion', 'Inscripción', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 0.00, 0, false, false, false, 10),
            $this->item('cuestionario', 'Cuestionario (Materia)', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 250.00, 1, false, true, false, 20),
            $this->item('registro', 'Registro', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 0.00, 0, false, false, false, 30),
            $this->item('material', 'Material Didáctico', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 0.00, 0, false, false, false, 40),
            $this->item('garantia', 'Garantía (Inscripción)', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 690.00, 0, true, true, false, 50),
            $this->item('cuestionario_final', 'Cuestionario Final', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 2490.00, 0, false, true, false, 60),
            $this->item('certificacion', 'Certificación', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 2190.00, 0, false, true, false, 70),
            $this->item('total', 'Pago Total', 'Secundaria 4 meses 16 sesiones', 'Estudiante Preferencial', 9370.00, 16, true, true, true, 80),

            // Secundaria - Estudiante Exclusivo
            $this->item('inscripcion', 'Inscripción', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 10),
            $this->item('cuestionario', 'Cuestionario (Materia)', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 1, false, false, false, 20),
            $this->item('registro', 'Registro', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 30),
            $this->item('material', 'Material Didáctico', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 40),
            $this->item('garantia', 'Garantía (Inscripción)', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 50),
            $this->item('cuestionario_final', 'Cuestionario Final', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 60),
            $this->item('certificacion', 'Certificación', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 0.00, 0, false, false, false, 70),
            $this->item('total', 'Pago Total', 'Secundaria 4 meses 16 sesiones', 'Estudiante Exclusivo', 8390.00, 16, true, true, true, 80),
        ];

        foreach ($items as $item) {
            DB::table('payment_items')->updateOrInsert(
                [
                    'course_type' => $item['course_type'],
                    'enrollment_type' => $item['enrollment_type'],
                    'code' => $item['code'],
                ],
                $item + ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    private function item(
        string $code,
        string $name,
        string $courseType,
        string $enrollmentType,
        float $amount,
        int $creditQty,
        bool $autoRegister,
        bool $isBillable,
        bool $unlockAllSubjects,
        int $sortOrder
    ): array {
        return [
            'code' => $code,
            'name' => $name,
            'course_type' => $courseType,
            'enrollment_type' => $enrollmentType,
            'amount' => $amount,
            'currency' => 'MXN',
            'credit_qty' => $creditQty,
            'auto_register' => $autoRegister,
            'is_active' => true,
            'is_billable' => $isBillable,
            'unlock_all_subjects' => $unlockAllSubjects,
            'sort_order' => $sortOrder,
        ];
    }
}
