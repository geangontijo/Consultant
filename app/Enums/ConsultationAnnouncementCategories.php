<?php

namespace App\Enums;

enum ConsultationAnnouncementCategories: string
{
    use EnumCasesAsStringArray;

    case Psychologist = 'psychologist';
    case Psychiatrist = 'psychiatrist';
    case Nutritionist = 'nutritionist';
    case LifeCoach = 'life_coach';
    case PersonalTrainer = 'personal_trainer';
    case YogaTeacher = 'yoga_teacher';
    case PilatesTeacher = 'pilates_teacher';
    case FitnessCoach = 'fitness_coach';
    case PersonalShopper = 'personal_shopper';
    case PersonalAssistant = 'personal_assistant';
    case PersonalDriver = 'personal_driver';
    case PersonalButler = 'personal_butler';
    case PersonalChef = 'personal_chef';
    case PersonalTutor = 'personal_tutor';
    case PersonalLawyer = 'personal_lawyer';
    case PersonalAccountant = 'personal_accountant';
    case PersonalFinancialAdvisor = 'personal_financial_advisor';
    case PersonalInvestmentAdvisor = 'personal_investment_advisor';
    case PersonalInsuranceAdvisor = 'personal_insurance_advisor';
    case PersonalMortgageAdvisor = 'personal_mortgage_advisor';
    case PersonalRealEstateAdvisor = 'personal_real_estate_advisor';
    case PersonalTaxAdvisor = 'personal_tax_advisor';
    case PersonalITAdvisor = 'personal_it_advisor';
    case PersonalMarketingAdvisor = 'personal_marketing_advisor';
    case PersonalBusinessAdvisor = 'personal_business_advisor';
    case PersonalBusinessCoach = 'personal_business_coach';
    case PersonalBusinessMentor = 'personal_business_mentor';
    case PersonalBusinessConsultant = 'personal_business_consultant';
    case PersonalBusinessBroker = 'personal_business_broker';
    case PersonalBusinessInvestor = 'personal_business_investor';
    case PersonalBusinessLender = 'personal_business_lender';

    public function getName(): string
    {
        /** @var ConsultationAnnouncementCategoriesPtBr $enumUnit */
        $enumUnit = constant(ConsultationAnnouncementCategoriesPtBr::class . '::' . $this->name);
        return $enumUnit->value;
    }
}
