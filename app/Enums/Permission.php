<?php

namespace App\Enums;

/** Permissions métier (Spatie). */
enum Permission: string
{
    case JobsManage = 'jobs.manage';
    case SectorsManage = 'sectors.manage';
    case ApplicationsViewAll = 'applications.view_all';
    case ApplicationsViewSector = 'applications.view_sector';
    case ApplicationsDecide = 'applications.decide';
    case OpinionsFirst = 'opinions.first';
    case InterviewsManage = 'interviews.manage';
    case InterviewsReport = 'interviews.report';
    case OpinionsInterview = 'opinions.interview';
    case RecruitmentValidate = 'recruitment.validate';
    case DashboardView = 'dashboard.view';
    case SynthesisView = 'synthesis.view';
    case HistoryView = 'history.view';
    case HistoryViewGlobal = 'history.view_global';
    case UsersManage = 'users.manage';
    case RolesManage = 'roles.manage';
    case NotificationsAll = 'notifications.all';

    /**
     * Permissions accordées à chaque rôle par le seeder.
     *
     * @return array<string, list<self>>
     */
    public static function matrix(): array
    {
        $sectorManager = [self::ApplicationsViewSector, self::OpinionsFirst, self::DashboardView, self::HistoryView];

        $direction = [
            self::JobsManage, self::SectorsManage, self::ApplicationsViewAll, self::ApplicationsDecide,
            self::InterviewsManage, self::InterviewsReport, self::OpinionsInterview,
            self::DashboardView, self::SynthesisView, self::HistoryView,
        ];

        $hr = [
            ...$direction,
            self::RecruitmentValidate, self::UsersManage, self::NotificationsAll, self::HistoryViewGlobal,
        ];

        return [
            Role::Member->value => [],
            Role::SectorManager->value => $sectorManager,
            Role::Direction->value => $direction,
            Role::Hr->value => $hr,
            Role::Admin->value => [self::RolesManage, self::HistoryViewGlobal, self::DashboardView],
        ];
    }
}
