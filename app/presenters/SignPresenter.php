<?php

namespace Clevis\Users;

use Nette;
use Nette\Forms\Form;
use Clevis\Skeleton\BasePresenter;


/**
 * Sign in/out presenter.
 */
class SignPresenter extends BasePresenter
{

	/**
	 * Sign-in form factory.
	 *
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new Nette\Application\UI\Form;

		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		$form->addCheckbox('remember', 'Keep me signed in');

		$form->addProtection();

		$form->addSubmit('send', 'Sign in');

		$form->onSuccess[] = $this->signInFormSucceeded;

		return $form;
	}


	public function signInFormSucceeded(Form $form)
	{
		$values = $form->getValues();

		if ($values->remember)
		{
			$this->getUser()->setExpiration('14 days', FALSE);
		}
		else
		{
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try
		{
			$this->getUser()->login($values->username, $values->password);
			$this->flashMessage('You have been signed in.');
			$this->redirect('Homepage:');
		}
		catch (Nette\Security\AuthenticationException $e)
		{
			$form->addError($e->getMessage());
		}
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('in');
	}

}
