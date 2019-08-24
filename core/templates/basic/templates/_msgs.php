 <div class="lcx-msgs">

                <div class="lcx-msg lcx--you lcx-msg--basic">
                     <div class="lcx-msg-container">
                             <div class="lcx-msg-time">12:30</div>
                             <div class="lcx-msg-content">Hello, how can i do this and do that?</div>
                     </div>
                        <div class="lcx-msg-meta">Sending...</div>
                </div>

                <!-- Operator message template -->
                <div class="lcx-msg lcx-row lcx-msg--basic">
                     <div class="lcx-msg-avatar lcx-col lcx-col--auto lcx-col--bottom">
                             <img src="https://pbs.twimg.com/profile_images/857529645058707457/rtSFxJfF_400x400.jpg" alt="">
                     </div>
                     <div class="lcx-msg-container lcx-col">
                             <div class="lcx-msg-time">12:30</div>
                             <div class="lcx-msg-content"><?php echo $msg['ops_offline_msg']; ?></div>
                     </div>
                </div>

                <!-- Are you customer? -->
                <div class="lcx-msg lcx-row lcx-msg--form">
                        <div class="lcx-msg-avatar lcx-col lcx-col--auto lcx-col--bottom">
                             <img src="https://pbs.twimg.com/profile_images/857529645058707457/rtSFxJfF_400x400.jpg" alt="">
                     </div>
                     <div class="lcx-msg-container lcx-col">
                                <div class="lcx-msg-content">Are you a Screets customer?</div>

                                <div class="lcx-btn-group">
                                        <a href="#" class="lcx-btn lcx-btn--narrow lcx-btn--lined lcx-btn--noAnim">Yes, i am a customer</a>
                                        <a href="#" class="lcx-btn lcx-btn--narrow lcx-btn--lined lcx-btn--noAnim">No, i am not yet</a>
                                </div>
                     </div>
                </div>

                <!-- Collector template -->
                <div class="lcx-msg lcx-row lcx--form">
                     <div class="lcx-msg-avatar lcx-col lcx-col--auto lcx-col--bottom">
                             <img src="https://pbs.twimg.com/profile_images/857529645058707457/rtSFxJfF_400x400.jpg" alt="">
                     </div>
                     <div class="lcx-msg-container lcx-col">
                         <?php 
                         $collector = @$chats['offline_collectorFields'];
                         if( !empty( $collector ) ): ?>
                                <div class="lcx-form lcx-form--collector">
                                    <div class="lcx-form-steps"></div>

                                    <?php foreach( $collector as $i => $name ): $type = ( $name == 'email' ) ? 'email' : 'text'; ?>
                            
                                            <div class="lcx-form-field lcx-field-<?php echo $name; ?> lcx-step-<?php echo $i+1; ?> lcx-form-field--suffix">
                                                    <div class="lcx-label"><?php echo $i+1; ?>. <?php echo $msg['forms_' . $name ]; ?></div>
                                                    
                                                    <div class="lcx-row">
                                                        <div class="lcx-field-wrap lcx-col">
                                                            <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="lcx-f-collector-<?php echo $name; ?>" class="lcx-input-text lcx-field lcx-f-collector-<?php echo $name; ?>" placeholder="<?php echo $msg['forms_' . $name ]; ?>">
                                                                                                    
                                                            <div class="lcx-valid-field"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/ok.svg' ); ?></div>
                                                        </div>
                                                        
                                                        <div class="lcx-col lcx-col--auto">
                                                                <a href="" class="lcx-btn lcx-btn lcx-btn--noAnim lcx-btn--narrow"><?php echo file_get_contents( LCX_PATH . '/assets/icons/chatbox/ok.svg' ); ?></a>
                                                        </div>
                                                    </div>
                                            </div>
                            
                                    <?php endforeach; ?>   
                                </div>
                        <?php endif; ?>
                     </div>
                </div>

                <!-- Select a topic -->
                <div class="lcx-msg lcx-row lcx-msg--basic">
                     <div class="lcx-msg-avatar lcx-col lcx-col--auto lcx-col--bottom">
                             <img src="https://pbs.twimg.com/profile_images/857529645058707457/rtSFxJfF_400x400.jpg" alt="">
                     </div>
                     <div class="lcx-msg-container lcx-col">

                                <div class="lcx-msg-content">Welcome back!</div>
                                <div class="lcx-msg-content">A few more details will help get you to the right person:</div>
                                
                                <div class="lcx-form">
                                        <div class="lcx-form-intro">Select a topic:</div>

                                        <div class="lcx-btn-group">
                                                <a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined lcx-btn--noAnim">Sales</a>
                                                <a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined lcx-btn--noAnim">Installing</a>
                                                <a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined lcx-btn--noAnim">Configuring</a>
                                                <a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined lcx-btn--noAnim">Licensing</a>
                                                <a href="#" class="lcx-btn lcx-btn--sm lcx-btn--lined lcx-btn--noAnim">Other</a>
                                        </div>
                                </div>
                     </div>
                </div>
                
                <!-- Visitor message template -->
                <div class="lcx-msg lcx--you lcx-msg--basic">
                     <div class="lcx-msg-container">
                             <div class="lcx-msg-time">12:30</div>
                             <div class="lcx-msg-content">Ne haber nasilsisniz mama bilemedim.</div>
                     </div>
                </div>

                
                <div class="lcx-msg lcx--you">
                     <div class="lcx-msg-container">
                             <div class="lcx-msg-time">12:30</div>
                             <div class="lcx-msg-content">Sorry, we aren't online at the moment, but keep the conversation going. We'll get back to you, asap</div>
                     </div>
                        <div class="lcx-msg-meta">Seen</div>
                </div>

        </div>