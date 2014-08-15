<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$labels = $this->langLabels->GetLangResult->Translates->Translate;

$label = new stdClass;
foreach($labels as $key => $value){
	if($value->LangCodes == 'da')
		$label = $value;
}
?>

<table align="center" style="width: 800px" class="style1">
	<tbody>
    	<tr>
            <td style="width: 800px">
                <table style="width: 800px" class="style2">
                    <tbody>
                    	<tr>
                            <td>          	

							</td>
                            <td class="MenuStyle">                                
                            	<font size="1" color="#666666" face="Verdana"> ● <b>
                                <font color="#666666">Hi, <?php echo $this->travelplan->GETCompleteOrderResult->Name->Name; ?>.</font>
                            </td>
                        </tr>
                        
                        <!-- <tr>
                            <td colspan="2">
                                <hr class="linered" style="height: 1px"></td>
                        </tr> -->
                        <!-- <tr>
                            <td class="MenuStyle" colspan="2">
                                <img src="http://online.traveloffice.dk/ImageVB.aspx?ImageID=L1" id="Image1">
                            </td>
                        </tr> -->
                    
                    </tbody>
                    </table>
                    <div class="auto-style7">
<p>
</p><table cellspacing="1" align="center" style="width: 100%">
	<tbody><tr>
		<td>
		<table cellspacing="3" cellpadding="6" align="center" style="width: 100%">
	<tbody><tr class="whiteback">
								<td align="left" colspan="2">
								
								<hr noshade="noshade" class="lineredbold">
								</td>
							</tr>
	<tr class="whiteback">
								<td align="left" colspan="2">
								
                                    <strong><strong class="bluehead">
                                    <?php echo $label->OrderVar->TravelTo; ?>
									<?php echo $this->travelplan->GETCompleteOrderResult->DestName." / ".$this->travelplan->GETCompleteOrderResult->Country->CountryName; ?> (<?php echo $this->travelplan->GETCompleteOrderResult->TravelStatus; ?>)</strong><hr style="color: #800000" class="linered">
								</strong></td>
							</tr>
	<tr>
								<td valign="top" style="height: 43px" class="style1">
									<span class="BlueCent">
										<?php echo $label->OrderVar->ThanksOffer; ?>
									</span>
								</td>
								<td valign="top" rowspan="2" style="width: 664px; " class="whiteback">
									<table style="width: 100%">
										<tbody>
											<tr>
												<td valign="top" class="auto-style16">
													<strong>
														<?php echo $label->OrderVar->TravelNo; ?>:
													</strong><br>
														<?php echo $label->OrderVar->TravelNoText; ?>
												</td>
												<td class="auto-style19" style="width: 200px">
													<?php echo $this->travelplan->GETCompleteOrderResult->OrderNo;?>
												</td>
											</tr>
											<tr>
												<td valign="top" colspan="2" class="style15">
												<hr style="height: 1px" class="linered">
												</td>
											</tr>
											<tr>
												<td valign="top" class="auto-style16">
													<strong><?php echo $label->OrderVar->ThisIsAnOffer; ?></strong><br>
													<?php echo $label->OrderVar->PaymentLatestText; ?>
												</td>
												<td class="auto-style20" style="width: 200px">
												<span class="auto-style21">
													<?php echo $label->OrderVar->OfferExp; ?>:<br>
												</span><?php echo date("m/d/Y", strtotime($this->travelplan->GETCompleteOrderResult->OfferDate)); ?></td>										
											</tr>                                    
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td valign="top" class="auto-style12" style="width: 33%; ">
									<table style="width: 100%">
										<tbody><tr>
											<td>
	                                         
											</td>
										</tr>
									</tbody></table>
								</td>
							</tr>
						</tbody>
					</table>
                                          
					<table cellspacing="3" cellpadding="6" align="center" style="width: 100%">
					<tbody>
						<tr>
							<td valign="top" colspan="2">
							<hr noshade="noshade" class="lineredbold"></td>
						</tr>
						<tr class="whiteback">
							<td align="left" style="width: 33%;">
								<font size="2" face="Verdana">	
								<span class="bluehead">
									<?php echo $label->OrderVar->Travel; ?>: <?php echo $this->travelplan->GETCompleteOrderResult->PnrNo; ?></span></font><hr style="color: #800000" class="linered">
							</td>
							<td align="left" style="width: 664px; ">						
                                <strong>
                                	<strong class="bluehead">
                                		<?php echo $label->CustVariables->ITEN_Route; ?>
                            		</strong>
                            		<hr style="color: #800000" class="linered">
								</strong>
							</td>
						</tr>
						<tr>
							<td valign="top" class="style12">
								<span class="BlueCent"><?php echo $label->OrderVar->Pax; ?>:<br></span>
							</td>
							<td valign="top" rowspan="2" style="width: 664px" class="whiteback">
                                <table style="width: 100%">
									<tbody>
										<tr>
										<td class="stylecenter" style="height: 40px" colspan="5">
										<!-- <img src="<?php echo $logo.'/AT.gif'; ?>" alt=""><hr class="auto-style10" style="height: 1px"></td> -->
									</tr>

									<?php foreach($this->travelplan->GETCompleteOrderResult->PNRlist->PNRlistD->segmentList->SegmentlistD as $travel): ?>
										<?php if($travel->Type == 'AIR'): ?>

		                                    <tr>
												<td valign="top" class="style12">
												<strong><?php echo date("m/d/Y", strtotime($travel->WinDate));?><br><?php echo $travel->Carrier." ".$travel->Flightno; ?></strong></td>
												<td valign="top" class="style12">
												<strong><?php echo $travel->DepCityName."<br>".$travel->ArrCityName; ?></strong><br>
												
												<span class="airline"><?php echo $travel->CarrierName; ?><br><br>
		                                        
		                                        </span></td>
												<td valign="top" class="style12"><?php echo $travel->Deptime; ?><br>
												<?php echo $travel->Arrtime; ?></td>
												<td valign="top" class="style12"><?php $deptermid = (isset($travel->Deptermid)) ? $travel->Deptermid : ""; echo $deptermid; ?><br></td>
												<td valign="top" class="style15"><?php echo $travel->Status; ?></td>
											</tr>
											<tr>
												<td colspan="5" class="style15">
												    &nbsp;</td>
											</tr>
										<?php elseif($travel->Type == 'HHL'): ?>
											<tr>
												<td valign="top" class="style12">
												<strong><?php echo date("m/d/Y h:i:s A", strtotime($travel->WinDate));?><br><?php echo date("m/d/Y", strtotime($travel->OutDate));?></strong></td>
												<td valign="top" class="style12">
												<strong><?php echo $travel->DepCityName; ?><br><?php echo $travel->SegName; ?></strong><br>
												<span class="airline"><?php echo $travel->VarText; ?><br></span></td>
												<td valign="top" class="style12"><br>
												</td>
												<td valign="top" class="style12">&nbsp;</td>
												<td valign="top" class="style15"><?php echo $travel->Status; ?></td>
											</tr>		
											<tr>
												<td colspan="5" class="style15">
											    &nbsp;</td>
											</tr>
										<?php endif; ?>
                                    <?php endforeach; ?>	
									</tbody></table>
								</td>
							</tr>
							<tr>
								<td valign="top" class="auto-style7" style="width: 33%">
	                                <br>
									<input type="button" id="PrintItenB" value="<?php echo $label->OrderVar->PrintIten; ?>" style="width: 100%" onclick="return popitup('<?php echo JURI::base().'index.php?option=com_travelplan&task=printDetail'; ?>')" name="Button1">
									<br>
								</td>
							</tr>
						</tbody>
					</table>
							<?php if(is_object($this->travelplan->GETCompleteOrderResult->InvoiceList)): ?>
								<?php if(is_object($this->travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD)): ?>
									<?php $invoice = $this->travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD; ?>
								<?php elseif(is_array($this->travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD)): ?>
									<?php $invoice = $this->travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD[0]; ?>
									<?php $payment = array(); ?>
									<?php if(is_array($this->travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD)): ?>
										<?php foreach($this->travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD as $k => $v): ?>
											<?php if($v->InvoiceType == 'PAYMENT') : ?>										
												<?php $payment[] = $v; ?>
											<?php endif; ?>
										<?php endforeach; ?>	
									<?php endif; ?>
								<?php endif; ?>	
							<table cellspacing="3" cellpadding="6" align="center" style="width: 100%">
	<tbody><tr>
								<td valign="top" colspan="2">
								<hr noshade="noshade" class="lineredbold"></td>
							</tr>
	<tr class="whiteback">
								<td align="left" colspan="2">
                                    <strong>
                                    	<strong class="bluehead">
	                                    	<?php echo $label->OrderVar->Invoice; ?>
											<?php echo $invoice->InvoiceNo; ?>
										</strong>
										<hr style="color: #800000" class="linered">
									</strong>
								</td>
							</tr>
	<tr>
								<td valign="top" class="auto-style8" style="width: 33%; ">
								<table style="width: 100%">
									<tbody>
										<tr>
											<td class="auto-style17" style="width: 130px">
												<?php echo $label->OrderVar->InvoiceNo; ?>:
											</td>
										
										<td class="style15"><?php echo $invoice->InvoiceNo; ?></td>
									</tr>
									<tr>
										<td class="auto-style17" style="width: 130px">
											<?php echo $label->OrderVar->IssueDate; ?>:
										</td>
										<td class="style15"><?php echo date("m/d/Y", strtotime($invoice->Issuedate)); ?></td>
									</tr>
									<tr>
										<td class="auto-style17" style="width: 130px">
											<?php echo $label->OrderVar->LastPayment; ?>:
										</td>
										<td class="auto-style22"><strong><?php echo date("m/d/Y", strtotime($invoice->paydate)); ?></strong></td>
									</tr>                                    

									<tr>
										<td style="width: 130px">&nbsp;</td>
										<td class="style15">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2">
								<input type="button" value="<?php echo $label->OrderVar->PrintInvoice; ?>" style="width: 100%" onclick="return popitup('<?php echo JURI::base().'index.php?option=com_travelplan&task=printFaktura'; ?>')" name="Button2"><br>
                                <!-- <input type="button" value="Bankoverførsel" style="width: 100%" onclick="MsgBox('du kan betale ved at overføre beløbet til vores konto. beløbet skal være hos selected tours senest på dagen for tilbudets udløb, hvis vi skal garanterer at billetten kan udstedes til den oplyste pris. vores konto nummer findes på fakturaen.')" name="Button4"> --><br>
										<strong>
										</strong></td>
									</tr>
								</tbody></table>
								</td>
								<td valign="top" style="width: 664px; height: 113px;" class="whiteback">
								<table style="width: 100%" class="auto-style9">
                                <?php if(is_object($invoice->InvoiceLinelist->InvoiceLinelistD)): ?>
									<tbody>
										<tr>
											<td valign="top" style="width: 5%" class="style15"><?php echo $invoice->InvoiceLinelist->InvoiceLinelistD->Units; ?></td>
											<td valign="top" style="width: 60%" class="style12">
											<?php echo $invoice->InvoiceLinelist->InvoiceLinelistD->Text; ?>
											</td>
											<td valign="top" style="width: 15%" class="style15">
											<?php echo number_format($invoice->InvoiceLinelist->InvoiceLinelistD->Unitprice, 2, '.', ','); ?></td>
											<td valign="top" style="width: 20%" class="style15">
											<strong><?php echo number_format($invoice->InvoiceLinelist->InvoiceLinelistD->Total, 2, '.', ','); ?></strong></td>
										</tr>
									</tbody>
								<?php elseif(is_array($invoice->InvoiceLinelist->InvoiceLinelistD)): ?>
									<tbody>
									<?php foreach($invoice->InvoiceLinelist->InvoiceLinelistD as $key => $value): ?>
										<tr>
											<td valign="top" style="width: 5%" class="style15"><?php echo $value->Units; ?></td>
											<td valign="top" style="width: 60%" class="style12">
											<?php echo $value->Text; ?>
											</td>
											<td valign="top" style="width: 15%" class="style15">
											<?php echo number_format($value->Unitprice, 2, '.', ','); ?></td>
											<td valign="top" style="width: 20%" class="style15">
											<strong><?php echo number_format($value->Total, 2, '.', ','); ?></strong></td>
										</tr>
									<?php endforeach; ?>
									</tbody>
								<?php endif;?>
								</table>
								</td>
							</tr>
	<tr>
								<td valign="top" class="auto-style12" style="width: 33%; ">
                                
								<span class="BlueCent"><?php echo $label->OrderVar->PaymentText;?>.</span><br><br>
                                </td>
								
                                <td valign="bottom" style="width: 664px" class="whiteback">
								<table style="width: 100%">
                                
									<tbody><tr>
										<td style="height: 19px" colspan="2" class="style15">
										<hr style="height: 1px" class="auto-style10">
										</td>
									</tr>
									<tr>
										<td class="style12">
										<strong class="auto-style17"><?php echo $label->OrderVar->TotalForInvoice . " " . $invoice->InvoiceNo; ?>:</strong></td>
										<td class="style15"><strong><?php echo number_format($invoice->Total, 2, '.', ','); ?></strong></td>
									</tr>
								</tbody></table>
								<?php endif; ?>
								</td>
							</tr>
						<tr>
								<td style="height: 34px" colspan="2">
								<hr noshade="noshade" class="lineredbold"></td>
							</tr>
							</tbody></table>
							
							<?php if(count($payment) > 0):?>
								<strong class="bluehead"><?php echo $label->OrderVar->Balance; ?></strong>
								<hr class="linered" style="color: #800000">
								<table cellspacing="3" cellpadding="6" align="center" style="width: 100%">
									<tbody>
										<tr align="left" style="height: 30px" colspan="2"> <td></td></tr>
										<tr>
											<td valign="top" rowspan="2" class="auto-style12" style="width: 33%; ">
												<table style="width: 100%">
													<tbody>
														<tr>
															<td class="auto-style13">&nbsp;</td>
														</tr>
													</tbody>	
												</table>                
												<span class="BlueCent"><br>Opgørelse over fakturaer og betalinger på denne rejse fremgår af listen her til højre.</span><br><br>                             
											</td>
											<td valign="top" style="width: 664px; height: 55px;">
												<table style="width: 100%" class="auto-style9">							                                
													<tbody>
														<tr>
															<td style="width: 60%; height: 17px;" class="style12">Faktura nr <?php echo $invoice->InvoiceNo; ?></td>
															<td style="width: 20%; height: 17px;" class="style15"><strong><?php echo number_format($invoice->Total, 2, '.', ','); ?></strong></td>
														</tr>						                                    
														<?php $totalpay = 0; ?>
														<?php foreach ($payment as $i => $payment_detail): ?>
															<tr>
																<td style="width: 60%; height: 17px;" class="style12">Betaling</td>
																<td style="width: 20%; height: 17px;" class="style15"><strong><?php echo number_format($payment_detail->Total, 2, '.', ','); ?></strong></td>
															</tr>					
															<?php $totalpay += $payment_detail->Total; ?>			                                    
														<?php endforeach; ?>
													</tbody>
												</table>
											</td>
										</tr>
										<tr>
											<td valign="bottom" style="width: 664px; height: 61px;" class="whiteback">
												<table style="width: 100%">
													<tbody>
														<tr>
															<td style="height: 17px" class="auto-style18"><strong>Restbeløb til betaling:</strong></td>
															<td style="width: 366px; height: 17px;" class="style15"><strong><?php echo number_format($invoice->Total+($totalpay), 2, '.', ','); ?></strong></td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="2"><hr noshade="noshade" class="lineredbold"></td>
										</tr>
									</tbody>
								</table>
							<?php endif; ?>

							<!-- Værktøj -->
							<!-- <table cellspacing="3" cellpadding="6" align="center" style="width: 100%">
								<tbody>
									<tr class="whiteback">
										<td align="left" colspan="2"><strong><strong class="bluehead">Værktøj</strong><hr style="color: #800000" class="linered"></strong></td>
									</tr>
									<tr>
										<td valign="top" class="auto-style8" style="width: 33%; "><span class="BlueCent">Du kan finde langt flere oplysninger om din rejse på Selected Tours's kundeportal. Hvis I er en virksomhed, indeholder kundeportalen mange gode funktioner, til at vedligeholde medarbejdernes FF kort, beregne rejsedage og meget mere.<br><br>Det er muligt at tegne en konkursdækning på rejser, der udelukkende består af flytransport, og som ikke er en forretningsrejse, skolerejse ect, men som udelukkende er en privat ferierejse. Konkursdækningen koster 20,- kr. pr. deltager. Hvis du ønsker at tegne en konkursdækning, skal du kontakte Selected Tours inden du betaler for rejsen..</span></td>
										<td valign="top" rowspan="2" style="width: 664px; " class="whiteback">						
			                                <table style="width: 100%">        
												<tbody>
													<tr>
														<td valign="top" colspan="2" class="style12"><hr style="height: 1px" class="auto-style10"></td>
													</tr>
													<tr>
														<td valign="top" class="auto-style16"><b>Check my Trip.</b><br>Se din rejse på flyselskabets eget system.</td>
														<td style="width: 200px">
															<input type="button" value="Check my Trip" style="width: 100%" onclick="return popcmt('')" name="Button5">
														</td>
													</tr>
													<tr>
														<td valign="top" colspan="2" class="auto-style16"><hr style="height: 1px" class="auto-style10"></td>
													</tr>
													<tr>
														<td valign="top" class="auto-style16"><b>Annuller rejsen.</b><br>Skal du alligevel ikke bruge rejsen, eller ændrede prisen sig i forhold til hvad du havde forventet, kan du her annullere din bestilling. Du kan ikke annullere en rejse, efter den er godkendt eller betalt.</td>
														<td style="width: 200px">
															<input type="submit" style="width:100%;" id="ctl18_Button19" value="Annuller rejsen" name="ctl18$Button19">
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td valign="top" class="auto-style12" style="width: 33%; "><span class="BlueCent"><strong>Hvis du overfører rejsen til din mobil telefon, har du altid dine rejsedokumenter ved hånden!</strong></span></td>
									</tr>
								</tbody>
							</table> -->
						</td>
					</tr>
				</tbody>
			</table>
<p></p>

					<br>
					</div>
					<table align="center" style="width: 100%">
						<tbody><!-- <tr>
							<td colspan="2">
							<hr class="lineredbold" style="height: 6px; width: 100%;"></td>
						</tr> -->
						<tr>
							<td valign="top" style="height: 100px">
								<br>
								<span class="bluebody"><?php echo $label->CustVariables->OnlineEndText; ?></span>
							</td>
							<td class="bluebodyRight">
								<strong><?php echo $this->travelplan->GETCompleteOrderResult->AgentName;?></strong><br>
								<?php echo $this->travelplan->GETCompleteOrderResult->AgentAddress;?><br><?php echo $this->travelplan->GETCompleteOrderResult->AgentCity;?>
								<br>
								<br>
								<?php echo $label->CustVariables->PhoneLabel . ": " . $this->travelplan->GETCompleteOrderResult->AgentPhone;?>							
								<br>
								<?php echo $label->CustVariables->EmailLabel . ": " . $this->travelplan->GETCompleteOrderResult->UserEmail;?>
								<br>
								<br>
								<strong><?php echo $label->CustVariables->EmailLabel; ?></strong>
							</td>
						</tr>
					</tbody></table>
                </td>
            </tr>
        </tbody></table>
