<?php
declare(strict_types=1);

namespace MeiKaiGsuit;


use phpseclib3\Crypt\EC\Formats\Keys\Common;

/**
 * @package
 * @property string $month
 * @property float|string $system_init
 * @property float|string $wallet_init
 * @property float $wallet_up_cashback
 * @property float $wallet_up_marketing
 * @property float $wallet_up_withdraw_return
 * @property float $wallet_up_check_return
 * @property float $wallet_up_paypal_return
 * @property float $wallet_up_other_return
 * @property float $wallet_down_cashback
 * @property float $wallet_down_marketing
 * @property float $wallet_down_withdraw_apply
 *
 * @property float|string $wallet_end
 * @property float|string $wallet_changed
 *
 * @property float|string $sp_init
 * @property float $sp_up_apply
 * @property float $sp_down_check_paid
 * @property float $sp_down_paypal_paid
 * @property float $sp_down_other_paid
 * @property float $sp_down_check_failed
 * @property float $sp_down_paypal_failed
 * @property float $sp_down_other_failed
 * @property float $sp_down_canceled
 * @property float $sp_down_fraud
 *
 * @property float|string $sp_end
 * @property float|string $sp_changed
 *
 * @property float|string $system_end
 *
 * 以下部分不同货币有不同的值
 * @property float $bank_check_up USD|AUD|GBP|EUR|JPY|CNY|CAD|KRW|RUB|HKD|TWD
 * @property float $bank_check_down USD|AUD|GBP|EUR|JPY|CNY|CAD|KRW|RUB|HKD|TWD
 * @property float $bank_paypal_up USD|AUD|GBP|EUR|JPY|CNY|CAD|KRW|RUB|HKD|TWD
 * @property float $bank_paypal_down USD|AUD|GBP|EUR|JPY|CNY|CAD|KRW|RUB|HKD|TWD
 * @property float $bank_charity  USD|HKD|TWD
 * @property float $bank_ach_up USD|HKD|TWD
 * @property float $bank_ach_down USD|HKD|TWD
 * @property float $bank_hsbc HKD
 * @property float $bank_other USD|AUD|GBP|EUR|JPY|CNY|CAD|KRW|RUB|HKD|TWD
 * //!CAD
 * @property float $sp_bank_diff
*/

class FinanceRow
{
    /**
     * @return string
     */
    public function getMonth(): ?string
    {
        return $this->month;
    }

    /**
     * @param string $month
     * @return FinanceRow
     */
    public function setMonth(string $month): FinanceRow
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getSystemInit()
    {
        return $this->system_init;
    }

    /**
     * @param float|string $system_init
     * @return FinanceRow
     */
    public function setSystemInit($system_init): FinanceRow
    {
        $this->system_init = $system_init;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getWalletInit()
    {
        return $this->wallet_init;
    }

    /**
     * @param float|string $wallet_init
     * @return FinanceRow
     */
    public function setWalletInit($wallet_init): FinanceRow
    {
        $this->wallet_init = $wallet_init;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletUpCashback(): ?float
    {
        return $this->wallet_up_cashback;
    }

    /**
     * @param float $wallet_up_cashback
     * @return FinanceRow
     */
    public function setWalletUpCashback(float $wallet_up_cashback): FinanceRow
    {
        $this->wallet_up_cashback = $wallet_up_cashback;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletUpMarketing(): ?float
    {
        return $this->wallet_up_marketing;
    }

    /**
     * @param float $wallet_up_marketing
     * @return FinanceRow
     */
    public function setWalletUpMarketing(float $wallet_up_marketing): FinanceRow
    {
        $this->wallet_up_marketing = $wallet_up_marketing;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletUpWithdrawReturn(): ?float
    {
        return $this->wallet_up_withdraw_return;
    }

    /**
     * @param float $wallet_up_withdraw_return
     * @return FinanceRow
     */
    public function setWalletUpWithdrawReturn(float $wallet_up_withdraw_return): FinanceRow
    {
        $this->wallet_up_withdraw_return = $wallet_up_withdraw_return;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletUpCheckReturn(): ?float
    {
        return $this->wallet_up_check_return;
    }

    /**
     * @param float $wallet_up_check_return
     * @return FinanceRow
     */
    public function setWalletUpCheckReturn(float $wallet_up_check_return): FinanceRow
    {
        $this->wallet_up_check_return = $wallet_up_check_return;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletUpPaypalReturn(): ?float
    {
        return $this->wallet_up_paypal_return;
    }

    /**
     * @param float $wallet_up_paypal_return
     * @return FinanceRow
     */
    public function setWalletUpPaypalReturn(float $wallet_up_paypal_return): FinanceRow
    {
        $this->wallet_up_paypal_return = $wallet_up_paypal_return;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletUpOtherReturn(): ?float
    {
        return $this->wallet_up_other_return;
    }

    /**
     * @param float $wallet_up_other_return
     * @return FinanceRow
     */
    public function setWalletUpOtherReturn(float $wallet_up_other_return): FinanceRow
    {
        $this->wallet_up_other_return = $wallet_up_other_return;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletDownCashback(): ?float
    {
        return $this->wallet_down_cashback;
    }

    /**
     * @param float $wallet_down_cashback
     * @return FinanceRow
     */
    public function setWalletDownCashback(float $wallet_down_cashback): FinanceRow
    {
        $this->wallet_down_cashback = $wallet_down_cashback;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletDownMarketing(): ?float
    {
        return $this->wallet_down_marketing;
    }

    /**
     * @param float $wallet_down_marketing
     * @return FinanceRow
     */
    public function setWalletDownMarketing(float $wallet_down_marketing): FinanceRow
    {
        $this->wallet_down_marketing = $wallet_down_marketing;
        return $this;
    }

    /**
     * @return float
     */
    public function getWalletDownWithdrawApply(): ?float
    {
        return $this->wallet_down_withdraw_apply;
    }

    /**
     * @param float $wallet_down_withdraw_apply
     * @return FinanceRow
     */
    public function setWalletDownWithdrawApply(float $wallet_down_withdraw_apply): FinanceRow
    {
        $this->wallet_down_withdraw_apply = $wallet_down_withdraw_apply;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getWalletEnd()
    {
        return $this->wallet_end;
    }

    /**
     * @param float|string $wallet_end
     * @return FinanceRow
     */
    public function setWalletEnd($wallet_end): FinanceRow
    {
        $this->wallet_end = $wallet_end;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getWalletChanged()
    {
        return $this->wallet_changed;
    }

    /**
     * @param float|string $wallet_changed
     * @return FinanceRow
     */
    public function setWalletChanged($wallet_changed): FinanceRow
    {
        $this->wallet_changed = $wallet_changed;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getSpInit()
    {
        return $this->sp_init;
    }

    /**
     * @param float|string $sp_init
     * @return FinanceRow
     */
    public function setSpInit($sp_init): FinanceRow
    {
        $this->sp_init = $sp_init;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpUpApply(): ?float
    {
        return $this->sp_up_apply;
    }

    /**
     * @param float $sp_up_apply
     * @return FinanceRow
     */
    public function setSpUpApply(float $sp_up_apply): FinanceRow
    {
        $this->sp_up_apply = $sp_up_apply;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownCheckPaid(): ?float
    {
        return $this->sp_down_check_paid;
    }

    /**
     * @param float $sp_down_check_paid
     * @return FinanceRow
     */
    public function setSpDownCheckPaid(float $sp_down_check_paid): FinanceRow
    {
        $this->sp_down_check_paid = $sp_down_check_paid;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownPaypalPaid(): ?float
    {
        return $this->sp_down_paypal_paid;
    }

    /**
     * @param float $sp_down_paypal_paid
     * @return FinanceRow
     */
    public function setSpDownPaypalPaid(float $sp_down_paypal_paid): FinanceRow
    {
        $this->sp_down_paypal_paid = $sp_down_paypal_paid;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownOtherPaid(): ?float
    {
        return $this->sp_down_other_paid;
    }

    /**
     * @param float $sp_down_other_paid
     * @return FinanceRow
     */
    public function setSpDownOtherPaid(float $sp_down_other_paid): FinanceRow
    {
        $this->sp_down_other_paid = $sp_down_other_paid;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownCheckFailed(): ?float
    {
        return $this->sp_down_check_failed;
    }

    /**
     * @param float $sp_down_check_failed
     * @return FinanceRow
     */
    public function setSpDownCheckFailed(float $sp_down_check_failed): FinanceRow
    {
        $this->sp_down_check_failed = $sp_down_check_failed;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownPaypalFailed(): ?float
    {
        return $this->sp_down_paypal_failed;
    }

    /**
     * @param float $sp_down_paypal_failed
     * @return FinanceRow
     */
    public function setSpDownPaypalFailed(float $sp_down_paypal_failed): FinanceRow
    {
        $this->sp_down_paypal_failed = $sp_down_paypal_failed;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownOtherFailed(): ?float
    {
        return $this->sp_down_other_failed;
    }

    /**
     * @param float $sp_down_other_failed
     * @return FinanceRow
     */
    public function setSpDownOtherFailed(float $sp_down_other_failed): FinanceRow
    {
        $this->sp_down_other_failed = $sp_down_other_failed;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownCanceled(): ?float
    {
        return $this->sp_down_canceled;
    }

    /**
     * @param float $sp_down_canceled
     * @return FinanceRow
     */
    public function setSpDownCanceled(float $sp_down_canceled): FinanceRow
    {
        $this->sp_down_canceled = $sp_down_canceled;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpDownFraud(): ?float
    {
        return $this->sp_down_fraud;
    }

    /**
     * @param float $sp_down_fraud
     * @return FinanceRow
     */
    public function setSpDownFraud(float $sp_down_fraud): FinanceRow
    {
        $this->sp_down_fraud = $sp_down_fraud;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getSpEnd()
    {
        return $this->sp_end;
    }

    /**
     * @param float|string $sp_end
     * @return FinanceRow
     */
    public function setSpEnd($sp_end): FinanceRow
    {
        $this->sp_end = $sp_end;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getSpChanged()
    {
        return $this->sp_changed;
    }

    /**
     * @param float|string $sp_changed
     * @return FinanceRow
     */
    public function setSpChanged($sp_changed): FinanceRow
    {
        $this->sp_changed = $sp_changed;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getSystemEnd()
    {
        return $this->system_end;
    }

    /**
     * @param float|string $system_end
     * @return FinanceRow
     */
    public function setSystemEnd($system_end): FinanceRow
    {
        $this->system_end = $system_end;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankCheckUp(): ?float
    {
        return $this->bank_check_up;
    }

    /**
     * @param float $bank_check_up
     * @return FinanceRow
     */
    public function setBankCheckUp(float $bank_check_up): FinanceRow
    {
        $this->bank_check_up = $bank_check_up;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankCheckDown(): ?float
    {
        return $this->bank_check_down;
    }

    /**
     * @param float $bank_check_down
     * @return FinanceRow
     */
    public function setBankCheckDown(float $bank_check_down): FinanceRow
    {
        $this->bank_check_down = $bank_check_down;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankPaypalUp(): ?float
    {
        return $this->bank_paypal_up;
    }

    /**
     * @param float $bank_paypal_up
     * @return FinanceRow
     */
    public function setBankPaypalUp(float $bank_paypal_up): FinanceRow
    {
        $this->bank_paypal_up = $bank_paypal_up;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankPaypalDown(): ?float
    {
        return $this->bank_paypal_down;
    }

    /**
     * @param float $bank_paypal_down
     * @return FinanceRow
     */
    public function setBankPaypalDown(float $bank_paypal_down): FinanceRow
    {
        $this->bank_paypal_down = $bank_paypal_down;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankCharity(): ?float
    {
        return $this->bank_charity;
    }

    /**
     * @param float $bank_charity
     * @return FinanceRow
     */
    public function setBankCharity(float $bank_charity): FinanceRow
    {
        $this->bank_charity = $bank_charity;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankAchUp(): ?float
    {
        return $this->bank_ach_up;
    }

    /**
     * @param float $bank_ach_up
     * @return FinanceRow
     */
    public function setBankAchUp(float $bank_ach_up): FinanceRow
    {
        $this->bank_ach_up = $bank_ach_up;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankAchDown(): ?float
    {
        return $this->bank_ach_down;
    }

    /**
     * @param float $bank_ach_down
     * @return FinanceRow
     */
    public function setBankAchDown(float $bank_ach_down): FinanceRow
    {
        $this->bank_ach_down = $bank_ach_down;
        return $this;
    }

    /**
     * @return float
     */
    public function getBankOther(): ?float
    {
        return $this->bank_other;
    }

    /**
     * @param float $bank_other
     * @return FinanceRow
     */
    public function setBankOther(float $bank_other): FinanceRow
    {
        $this->bank_other = $bank_other;
        return $this;
    }

    /**
     * @return float
     */
    public function getSpBankDiff(): ?float
    {
        return $this->sp_bank_diff;
    }

    /**
     * @param float $sp_bank_diff
     * @return FinanceRow
     */
    public function setSpBankDiff(float $sp_bank_diff): FinanceRow
    {
        $this->sp_bank_diff = $sp_bank_diff;
        return $this;
    }

    public function toArray()
    {
        return [
            $this->getMonth(), //A
            $this->getSystemInit() ?? '=AB`BR2`', //B '=AB3' row_index = 4
            $this->getWalletInit() ?? '=M`BR2`', //C '=M3' row_index = 4

            $this->getWalletUpCashback() ?? 0, //D
            $this->getWalletUpMarketing() ?? 0, //E
            $this->getWalletUpWithdrawReturn() ?? 0, //F
            $this->getWalletUpCheckReturn() ?? 0, //G
            $this->getWalletUpPaypalReturn() ?? 0, //H
            $this->getWalletUpOtherReturn() ?? 0, //I
            $this->getWalletDownCashback() ?? 0, //J
            $this->getWalletDownMarketing() ?? 0, //K
            $this->getWalletDownWithdrawApply() ?? 0, //L

            $this->getWalletEnd() ?? '=C`CR`+SUM(D`CR`:I`CR`)-sum(J`CR`:L`CR`)', //M '=C4+SUM(D4:I4)-sum(J4:L4)' row_index = 4
            $this->getWalletChanged() ?? '=M`CR`-C`CR`', //N '=M4-C4' row_index = 4
            $this->getSpInit() ?? '=Z`BR2`', //O  '=Z3' row_index = 4


            $this->getSpUpApply() ?? 0, //P
            '', // 空列 //Q
            $this->getSpDownCheckPaid() ?? 0, //R
            $this->getSpDownPaypalPaid() ?? 0, //S
            $this->getSpDownOtherPaid() ?? 0, //T

            $this->getSpDownCheckFailed() ?? 0, //U
            $this->getSpDownPaypalFailed() ?? 0, //V
            $this->getSpDownOtherFailed() ?? 0, //W
            $this->getSpDownCanceled() ?? 0, //X
            $this->getSpDownFraud() ?? 0, //Y

            $this->getSpEnd() ?? '=O`CR`+P`CR`-SUM(R`CR`:Y`CR`)', //Z '=O4+P4-SUM(R4:Y4)' row_index = 4
            $this->getSpChanged() ?? '=Z`CR`-O`CR`', //AA '=Z4-O4' row_index = 4
            $this->getSystemEnd() ?? '=Z`CR`+M`CR`', //AB '=Z4+M4' row_index = 4

            $this->getBankCheckUp() ?? 0,
            $this->getBankCheckDown() ?? 0,
            $this->getBankPaypalUp() ?? 0, //AE
            $this->getBankPaypalDown() ?? 0, //AF
//            $this->getBankAchUp() ?? 0, //AC
//            $this->getBankAchDown() ?? 0, //AD
//            $this->getBankOther() ?? 0, // AG
//            '', // 空列 //AH
//            $this->getSpBankDiff() ?? '', //AI
        ];
    }

}