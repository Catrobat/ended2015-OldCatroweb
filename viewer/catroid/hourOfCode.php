<?php
/*
 * Catroid: An on-device visual programming system for Android devices
 * Copyright (C) 2010-2014 The Catrobat Team
 * (<http://developer.catrobat.org/credits>)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * An additional term exception under section 7 of the GNU Affero
 * General Public License, version 3, is available at
 * http://developer.catrobat.org/license_additional_term
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
?>
<article>
  <div class="header">
    <div class="headerLarge">
      <?php echo $this->languageHandler->getString('title'); ?>
    </div>     
  </div> 
  <div class="clear"></div>
  <div class="hourOfCodeMain">
    <?php for($i=1;$i<21;$i++) {?>
    <div id="content<?php echo $i;?>" class="hourOfCodeMainContent<?php echo  $i!=1?" hide":" "?>">
        <div class="detailHeaderSide" onclick="prev(<?php echo $i; ?>);"><div class="arrow left"></div></div>
        <div id="detailHeaderNavigation">
          <div class="detailHeaderNav">
            <?php for($j=1;$j<=20;$j++) {?>
              <a class="stepLinks navigation<?php echo $j; ?>" onclick="changeContainer(<?php echo $j; ?>);"><div class="linkBackground"> <?php echo $j; ?></div></a>
            <?php }?>
          </div>
        </div>
        <div class="detailHeaderSide" onclick="next(<?php echo $i; ?>);"><div class="arrow right"></div></div>
        <div class="clear"></div>
        <div class="detailHeaderCenter"><?php echo $this->languageHandler->getString('title'.$i); ?></div>
        <div class="detailDescription"><?php echo $this->languageHandler->getString('description'.$i); ?></div>
        <div class="detailContainer">
        <?php if($i==4||$i==7||$i==8||$i==11||$i==14) {
          if($i==11) { ?>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
            <div><?php echo $this->languageHandler->getString('imageText'.$i."_1"); ?></div>
          </div>
          <div class="detailSpacer"><div class="arrow right"></div></div>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
            <ol>
              <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_1"); ?></li>
              <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_2"); ?></li>
              <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_3"); ?></li>
            </ol>
          </div>
          <div class="detailSpacer"><div class="arrow right"></div></div>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
            <div><?php echo $this->languageHandler->getString('imageText'.$i."_3"); ?></div>
          </div>
          <div class="detailSpacer"><div class="arrow right"></div></div>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_4"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 4);'><img src="images/hourOfCode/thumbs/<?php echo $i."_4.jpg"?>" /></span><br />
            <div><?php echo $this->languageHandler->getString('imageText'.$i."_4"); ?></div>
          </div>
        <?php }
        else { ?>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
            <div><?php echo $this->languageHandler->getString('imageText'.$i."_1"); ?></div>
          </div>
          <div class="detailSpacer"><div class="arrow right"></div></div>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
            <div><?php echo $this->languageHandler->getString('imageText'.$i."_2"); ?></div>
          </div>
          <div class="detailSpacer"><div class="arrow right"></div></div>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
            <div><?php echo $this->languageHandler->getString('imageText'.$i."_3"); ?></div>
          </div>
          <div class="detailSpacer"><div class="arrow right"></div></div>
          <div class="detailImage4">
            <span id="image_<?php echo $i."_4"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 4);'><img src="images/hourOfCode/thumbs/<?php echo $i."_4.jpg"?>" /></span><br />
            <div><?php echo $this->languageHandler->getString('imageText'.$i."_4"); ?></div>
          </div>
        <?php }
        }
        else { ?>
          <?php if($i == 9) {?>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
              <ol>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_1_1"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_1_2"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_1_3"); ?></li>
              </ol>
            </div>
            <div class="detailSpacer"><div class="arrow right"></div></div>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
              <ol>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_1"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_2"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_3"); ?></li>
              </ol>
            </div>
            <div class="detailSpacer"><div class="arrow right"></div></div>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
              <div><?php echo $this->languageHandler->getString('imageText'.$i."_3"); ?></div>
            </div>
          <?php }
          else if($i==10) { ?>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
              <ol>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_1_1"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_1_2"); ?></li>
              </ol>
            </div>
            <div class="detailSpacer"><div class="arrow right"></div></div>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
              <ol>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_1"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_2"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_3"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_4"); ?></li>
                <li><?php echo $this->languageHandler->getString('imageText'.$i."_2_5"); ?></li>
              </ol>
            </div>
            <div class="detailSpacer"><div class="arrow right"></div></div>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
              <div><?php echo $this->languageHandler->getString('imageText'.$i."_3"); ?></div>
            </div>
          <?php }
          else if($i==16) { ?>
            <div class="detailImage3">
                <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
                <div><?php echo $this->languageHandler->getString('imageText'.$i."_1"); ?></div>
              </div>
              <div class="detailSpacer"><div class="arrow right"></div></div>
              <div class="detailImage3">
                <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
                <div><?php echo $this->languageHandler->getString('imageText'.$i."_2"); ?></div>
              </div>
              <div class="detailSpacer"><div class="arrow right"></div></div>
              <div class="detailImage3">
                <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
                <ol>
                  <li><?php echo $this->languageHandler->getString('imageText'.$i."_3_1"); ?></li>
                  <li><?php echo $this->languageHandler->getString('imageText'.$i."_3_2"); ?></li>
                  <li><?php echo $this->languageHandler->getString('imageText'.$i."_3_3"); ?></li>
                </ol>
              </div>
          <?php }
          else if($i==17) { ?>
            <div class="detailImage3">
                <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
                <div><?php echo $this->languageHandler->getString('imageText'.$i."_1"); ?></div>
              </div>
              <div class="detailSpacer"><div class="arrow right"></div></div>
              <div class="detailImage3">
                <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
                <div><?php echo $this->languageHandler->getString('imageText'.$i."_2"); ?></div>
              </div>
              <div class="detailSpacer"><div class="arrow right"></div></div>
              <div class="detailImage3">
                <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
                <div><?php echo $this->languageHandler->getString('imageText'.$i."_3_1"); ?></div>
                <ol>
                  <li><?php echo $this->languageHandler->getString('imageText'.$i."_3_2"); ?></li>
                  <li><?php echo $this->languageHandler->getString('imageText'.$i."_3_3"); ?></li>
                  <li><?php echo $this->languageHandler->getString('imageText'.$i."_3_4"); ?></li>
                </ol>
              </div>
          <?php }
          else if($i==18 || $i==20) { ?>
            <div class="detailImage3">
                  <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
                  <ol>
                    <li><?php echo $this->languageHandler->getString('imageText'.$i."_1_1"); ?></li>
                    <li><?php echo $this->languageHandler->getString('imageText'.$i."_1_2"); ?></li>
                  </ol>
                </div>
                <div class="detailSpacer"><div class="arrow right"></div></div>
                <div class="detailImage3">
                  <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
                  <div><?php echo $this->languageHandler->getString('imageText'.$i."_2"); ?></div>
                </div>
                <div class="detailSpacer"><div class="arrow right"></div></div>
                <div class="detailImage3">
                  <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
                  <div><?php echo $this->languageHandler->getString('imageText'.$i."_3"); ?></div>
                </div>
          <?php }
          else { ?>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_1"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 1);'><img src="images/hourOfCode/thumbs/<?php echo $i."_1.jpg"?>" /></span><br />
              <div><?php echo $this->languageHandler->getString('imageText'.$i."_1"); ?></div>
            </div>
            <div class="detailSpacer"><div class="arrow right"></div></div>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_2"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 2);'><img src="images/hourOfCode/thumbs/<?php echo $i."_2.jpg"?>" /></span><br />
              <div><?php echo $this->languageHandler->getString('imageText'.$i."_2"); ?></div>
            </div>
            <div class="detailSpacer"><div class="arrow right"></div></div>
            <div class="detailImage3">
              <span id="image_<?php echo $i."_3"?>" onclick='showImage(<?php echo "\"".BASE_PATH."\"".",".$i; ?>, 3);'><img src="images/hourOfCode/thumbs/<?php echo $i."_3.jpg"?>" /></span><br />
              <div><?php echo $this->languageHandler->getString('imageText'.$i."_3"); ?></div>
            </div>
          <?php }?>
        <?php }?>
        </div>
    </div>
    <?php }?>
  </div> 
  <div id="imageOverlay"></div>
  <div id="imagePopup">
    <div id="outerContainer">
      <div id="container">bild</div>
    </div>
  </div>
</article>
<script>
  $(document).ready(function() {
      initHourOfCode();
  });
</script>
