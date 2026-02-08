export type CalendarEventInput = {
  event_code?: string | null;
  title: string;
  description?: string | null;
  due_date: string;
  is_payment?: boolean;
  is_active?: boolean;
};

export type CalendarTemplate = {
  id: number;
  name: string;
  description?: string | null;
  is_active: boolean;
  events: Array<{
    id: number;
    title: string;
    due_date: string;
    is_payment: boolean;
    is_active: boolean;
  }>;
};

export type CohortRuleInput = {
  field: string;
  operator: 'equals' | 'not_equals' | 'in' | 'not_in';
  value: string;
  is_active?: boolean;
};

export type Cohort = {
  id: number;
  name: string;
  course_type: string | null;
  enrollment_type: string | null;
  calendar_template_id: number | null;
  capacity: number | null;
  starts_at: string | null;
  ends_at: string | null;
  auto_assign: boolean;
  is_active: boolean;
  active_members_count?: number;
  waitlist_members_count?: number;
  rules?: Array<{
    id: number;
    field: string;
    operator: string;
    value: string;
    is_active: boolean;
  }>;
};

export type StudentLookup = {
  id: number;
  first_name: string;
  surnames: string;
  email: string;
};
