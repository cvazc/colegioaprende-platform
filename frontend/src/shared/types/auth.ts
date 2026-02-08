export type ApiRole = 'employee' | 'student';

export type EmployeeRoleLabel = 'admin' | 'staff';

export type EmployeePermissions = Record<string, boolean>;

export type EmployeeUser = {
  id: number;
  email: string;
  first_name: string;
  surnames: string;
  employee_type: number;
  role_label?: EmployeeRoleLabel;
  permissions?: EmployeePermissions;
};

export type StudentUser = {
  id: number;
  email: string;
  first_name: string;
  surnames: string;
  status?: string | null;
  profile_completed_at?: string | null;
};

export type AuthUser = EmployeeUser | StudentUser;

export type AuthSession = {
  token: string;
  role: ApiRole;
  user: AuthUser;
};

export type LoginPayload = {
  email: string;
  password: string;
  recaptcha_token?: string;
};
