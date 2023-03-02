<?php

namespace App\Enums;

enum ConsultationAnnouncementCategoriesPtBr: string
{
    use EnumCasesAsStringArray;

    case Psychologist = 'Psicólogo';
    case Psychiatrist = 'Psiquiatra';
    case Nutritionist = 'Nutricionista';
    case LifeCoach = 'Coach de vida';
    case PersonalTrainer = 'Personal trainer';
    case YogaTeacher = 'Professor de yoga';
    case PilatesTeacher = 'Professor de pilates';
    case FitnessCoach = 'Coach de fitness';
    case PersonalShopper = 'Personal shopper';
    case PersonalAssistant = 'Assistente pessoal';
    case PersonalDriver = 'Motorista pessoal';
    case PersonalButler = 'Mordomo';
    case PersonalChef = 'Chef pessoal';
    case PersonalTutor = 'Professor particular';
    case PersonalLawyer = 'Advogado';
    case PersonalAccountant = 'Contador';
    case PersonalFinancialAdvisor = 'Assessor financeiro';
    case PersonalInvestmentAdvisor = 'Assessor de investimentos';
    case PersonalInsuranceAdvisor = 'Assessor de seguros';
    case PersonalMortgageAdvisor = 'Assessor de empréstimos';
    case PersonalRealEstateAdvisor = 'Assessor imobiliário';
    case PersonalTaxAdvisor = 'Assessor tributário';
    case PersonalITAdvisor = 'Assessor de TI';
    case PersonalMarketingAdvisor = 'Assessor de marketing';
    case PersonalBusinessAdvisor = 'Assessor de negócios';
    case PersonalBusinessCoach = 'Coach de negócios';
    case PersonalBusinessMentor = 'Mentor de negócios';
    case PersonalBusinessConsultant = 'Consultor de negócios';
    case PersonalBusinessBroker = 'Corretor de negócios';
    case PersonalBusinessInvestor = 'Investidor de negócios';
    case PersonalBusinessLender = 'Emprestador de negócios';
}
