<?php


namespace CurdGen\Mode;


interface IMode
{

    public function modelValidate($columns_set);
    public function modelAuto($columns_set);

    public function tableColumn($columns_set);
    public function formColumn($columns_set);
    public function formExtra($columns_set);
    public function editColumn($columns_set);
    public function saveColumn($columns_set);

    public function injectAddTopButton();
    public function injectEditRightButton();
    public function injectSaveTopButton();

    public function funSuccessJumpUrl();
    public function funFormDisplay();
    public function editMetaTitle();
    public function addMetaTitle();
    public function editExtraFun();
    public function addExtraFun();
    public function tableBtnPlaceholder();

}