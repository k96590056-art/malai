<template>
  <div id="__next" style="overflow-y: auto">
    <div class="entry_entryStyle__FXwIQ">
      <div class="entry_entryMain__2anz2 entry_loginAnimated__1Gwda" v-if="infoType == 0">
        <div style="width: 100vw">
          <div class="login_formContainer__2zhUW">
            <div class="login_normalContent__1I3Xg">
              <div class="login_title__1VIs3">登录</div>
              <div class="login_form__1BQh1">
                <div class="login_normalLoginWrap__Q4P0O">
                  <div class="login_inputBox__3j84z">
                    <div class="login_inputGroup__1xPH8">
                      <div class="login_accountIcon__1eker"></div>
                      <div class="login_inputMain__18b4V"><input maxlength="32" v-model="loginInfo.name" placeholder="用户名" class="login_input__hMBpm" type="text" autocomplete="off" /></div>
                      <div class="login_operateBtn__25zoY login_deleteBtn__Tamqm" style="opacity: 0"></div>
                    </div>
                    <div class="login_inputGroup__1xPH8">
                      <div class="login_passwordIcon__foPzR"></div>
                      <div class="login_inputMain__18b4V"><input placeholder="密码" v-model="loginInfo.password" maxlength="32" class="login_input__hMBpm" type="password" autocomplete="off" /></div>
                      <div class="login_operateBtn__25zoY login_hidePwdBtn__3EBnN" style="opacity: 0"></div>
                    </div>
                  </div>

                  <div class="login_btnGroup__37-ja"><button style="background: #1890ff" @click="login" class="login_activeBtn__3wLDx" type="button" data-analytics="button" data-label="登录" data-key="yb_lgn_btn_tap">登录</button></div>
                  <div class="login_visitorBtn__2xo1h"><a data-analytics="button" @click="$parent.goNav('/')" data-label="先去逛逛" data-key="yb_tour">先去逛逛</a><a @click="$parent.openKefu">在线客服</a></div>
                </div>
              </div>
            </div>
            <div class="login_locationToRight__gIQoN" @click="changInfoType(1)">
              <div class="login_locationContent__o3pkw">
                <div class="login_linkRegisterIcon__3AVOn"></div>
                <div class="login_text__-W8Ok"><span>注</span><span>册</span><span>新</span><span>用</span><span>户</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="infoType == 1" class="entry_entryMain__2anz2 entry_registerAnimated__2Bdsz entry_transition__38Yv4">
        <div style="width: 100vw; margin-left: 100vw">
          <div class="register_formContainer__3DhlE">
            <div class="register_locationToLeft__17bW-" @click="changInfoType(0)">
              <div class="register_locationContent__2IbiZ">
                <div class="register_linkRegisterIcon__bUBqC"></div>
                <div class="register_text__33mcY"><span>返</span><span>回</span><span>登</span><span>录</span></div>
              </div>
            </div>
            <div class="register_registerContent__16A8K">
              <div class="register_registerTitle__gkT0e">注册</div>
              <div class="register_form__1pmZ3">
                <div class="register_inputBox__6n6TQ">
                  <div class="register_inputGroup__2VkRD">
                    <div class="register_inputIcon__2arpa register_accountIcon__27e07"></div>
                    <div class="register_inputMain__3Ur5m">
                      <input v-model="registerInfo.name" placeholder="用户名" maxlength="32" data-inputtype="register" class="register_input__1ukwi" type="text" />
                    </div>
                    <div class="register_operateBtn__12YnF register_deleteBtn__2EnTW" style="opacity: 0"></div>
                  </div>

                  <div class="register_inputGroup__2VkRD">
                    <div class="register_inputIcon__2arpa register_passwordIcon__18fIi"></div>
                    <div class="register_inputMain__3Ur5m"><input placeholder="登录密码" v-model="registerInfo.password" maxlength="32" data-inputtype="register" class="register_input__1ukwi" type="password" autocomplete="off" /></div>
                    <div class="register_operateBtn__12YnF register_hidePwdBtn__Qkz7h" style="opacity: 0"></div>
                  </div>
                  <div class="register_inputGroup__2VkRD">
                    <div class="register_inputIcon__2arpa register_passwordIcon__18fIi"></div>
                    <div class="register_inputMain__3Ur5m"><input placeholder="确认密码" v-model="registerInfo.confirmPass" data-inputtype="register" maxlength="32" class="register_input__1ukwi" type="password" autocomplete="off" /></div>
                    <div class="register_operateBtn__12YnF register_hidePwdBtn__Qkz7h" style="opacity: 0"></div>
                  </div>
                  <div class="register_inputGroup__2VkRD">
                    <div class="register_inputIcon__2arpa register_accountIcon__27e07"></div>
                    <div class="register_inputMain__3Ur5m"><input v-model="registerInfo.realname" placeholder="真实姓名" maxlength="32" data-inputtype="register" class="register_input__1ukwi" type="text" /></div>
                    <div class="register_operateBtn__12YnF register_deleteBtn__2EnTW" style="opacity: 0"></div>
                  </div>
                  <div class="register_inputGroup__2VkRD">
                    <div class="register_inputIcon__2arpa register_passwordIcon__18fIi"></div>
                    <div class="register_inputMain__3Ur5m"><input placeholder="支付密码" v-model="registerInfo.paypassword" maxlength="32" data-inputtype="register" class="register_input__1ukwi" type="password" autocomplete="off" /></div>
                    <div class="register_operateBtn__12YnF register_hidePwdBtn__Qkz7h" style="opacity: 0"></div>
                  </div>
                </div>
                <div class="register_ruleContainer__1h-YD"></div>

                <div class="register_btnGroup__1vkHI"><button style="background: #1890ff" class="register_registerBtn__1set3" @click="register" type="button" data-key="yb_rgstr_btn_tap" data-analytics="button" data-label="注册">注册</button></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="display: none" id="app-footer" class="footer_footer_bg_hidden__1QCjG">
      <ul>
        <li class="" data-analytics="button" data-label="首页">
          <i></i>
          <div style="cursor: inherit" class="image_imageContainer__2dKjX image_cover__3B5D- footer_icon__1Rh5j">
            <div style="display: block; overflow: hidden; position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; margin: 0px">
              <img
                class="footer_icon__1Rh5j"
                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEVklEQVRoge2ZT2zbVBzHAxoIEBxgAk0CIY4gxAWkSQghgcQFBDshIXFjlzEIF24goQmhHcoJoYnTOHBaWlg1mO2kSZv37KSxn5NtbVBI/ziJnffshGRts7GtZRR+yKUJiZtgu423TuQnfQ+R8/Q+3zy/9/vaCYVGNapRDbUKBbgXE/YxJuw8JqyKFbqCFYYwoV/OztcfC+3nQln6CiJsASkU+gkTuoZVdhwA7grttxIJO4YJ+3MQfK8R9h0COBDaL4VV9pkX8G4lZcrnctYDtxUcAO5GKj3lF356lgKHDeCxLk8TdvB2btZxJ5w6X4elyhpIKusPn64Ch/R/hfViIm09eUvhUaHxIFZYwgk3V2zA7zc3wa7FyuoO+IQTHrVlsFjaePaWwEsXa48iheWccPmFJvx2/Sasb/yxJWWu1nM9nhoEr2+vhLEqSMZLgcKjS7WnsMKWnPA/LzZhpbUOrasbsHZlA4gDfsoNHnVM3IiK1SOBwIu5+nOYUKsfvNW4Bo2V61BrXIPZC2bP9ZhXeNS5nTYFsXp0qPCYWC/bTcgJP7/QhAq9ArR2Fcq0BVK2d+NGJcMnvN6RgKufDgVezJpHsELXexqRQuFi4VdYKK+CZqxBUVuxu2wvvLh7eK5jwvh6T11bVMyjSGGbTvhsvg7zxSYUli7D3C+N3tNGHg48ty0eGRH7yPYNj1Xzg37nuH2Pq/n61gqo872bNSlTEPBwwLneXsEj5CN6YIW9OijXpHImyJesHZt1RqbAiwHAIx3OJ3WI8NpXPgzQ/H+Hsd7PMxlqx4JA4H9KVuAMvwwRTtv8YarytCt8SrFe8JVr5Kq9xIHBR7hlOLOtcU476f7rE/Mjz/CZ4ODPzVQ64B3xy9iDAXbSW6IMEH66vBOeW7b3waIHA3TMDT4xawQGPxkv9YXfVmnPBuI2fADgHHaF37uBeCo4+LNTrvB7MzAVFDzS4fuoJ/jdG4h5CWVYN+zswmPjEzdxWP+GQ3r9H3jNK/zuDMQk91+QR0Y6nW4+FPJRsQx9ZELQ8j7g/RlI+ojDMYkebo/PULgfZc3XkMLe7padajNzlx/vnms8WnojEANJuQqCj1yTy8E9ncdNQrWBEUSh61g132rPNcFXDg3fgEzHBJ9xuD1WJOYJtx5i56z29yen2cGhGxAkY8zvKdIeiwg97drFCW0FaoDHIwMwWgFudAvp+3ITn42X79xNPBkvAVaqd6aBc4ny1usaTKinPvD5fjLw43Sl+2VCwdUAlunrvA94Huu19lhRYV+4d2JW7BhGcCDCaTcGwdsP9Q7zp0NeKpmh33p5ZBRE46+ZDA13gBT2BCa0NtgA2xSJ9W73XON86UQ/eA454dkCUhuHQl5rJkMPR0XjmID0cD/xUvV9KWc97xyXyrceFlXzHftPv25h1XxPvGA902+uiejyixGhdDwiaOEJQQvHJD2MiLklUTE/RIS9iSpwn2f4UY3qf1R/A/1BRmX2Ea0WAAAAAElFTkSuQmCC"
                decoding="async"
                style="position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%; object-fit: cover"
              />
            </div>
          </div>
          <span class="">首页</span>
        </li>
        <li class="" data-analytics="button" data-label="优惠">
          <i></i>
          <div style="cursor: inherit" class="image_imageContainer__2dKjX image_cover__3B5D- footer_icon__1Rh5j">
            <div style="display: block; overflow: hidden; position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; margin: 0px">
              <img
                class="footer_icon__1Rh5j"
                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEUElEQVRoge2ZTWwbRRiGDRTooUJAhUBISIgT4lCERLjCCfGjnkAcEIgLEj30hoRQhUAcoBIXuJBL4YCEZOMEldLZdVLbmVm7dnbWTtsEWYmwY+/uzHpdJ2liuQ0OpHxoNzGEFLx/Y35Uf9J7sOSdfZ7RzOzndSw2qlGNyrMA4BaisQ8JZU1CeZtQ/hXR7LGYgCpWOvcSar2DKV8ilHUIZal86fIjMZGFNf4eVhnsD6F8VqHNV8tluD3omLk5doRQdoqobPPGcVkdAxwQJuDM/F8J7LlhU6HWB1hrPzBoHAC4LVdiL2HKyKDxsrMMZGIcFQKPK+1Dg262T2QLU/Z1rmQ/tXcMSvlhrLF3CeWm1xjZIgOJ6CAr5ttCBFwJlXG/EntkTrnXltgzROVdP9dkCyZIWAfkJKe/KExA0fgrWOXbwST4Nm7AQULZl36+nykwQMRw4SWip5yDQ5iAK1Gyntg5ifi4r6j8dec6XG4+qmj800HfTRfMcUT0cQkbnyFsvObsFaHw/9sCgFsrFbjj30o5xPHsFtaaz2GVlwllPwfdvH4znd9Z78gjEjGYRIxPMIaDvuAVtfnssKD7mfIJj/aKYGPSl4DzdB0qfC44PNpNKseOeC8fynpDgZ81IZULB476AorxhrfAEOBnVAZyhJlH/WWkGG/94wIzTl+jRANHQQQI5UwoPBEDj9wlxF7wFlD55yLgnY5SEgiPsLGK8frdngKqunYXobwQCb5oCoWXsL4hE/Z8LMgTmGjWUaJa7yuqdTJIskV2UlIMMSHGxxLW3zx33nwwdlNXfmHjntwF+z6vyDlxKYftg/rltLSE8hNY5S2vNZ8+bwrcrHp/3fcQ0aXTuPFwKAFC+Ud+Nuy5IcCjHQGYnFqGhFRrJHH7UCD4ahXudH/f7kLSeRt4qwut1WvA7S40eAd+bKzDwtIq0PkW5EtNkJXoT1rUD9FhYmoZ4qjq5htp+VgggTzlj/fhSwstuH79V+htbUOnuwXttU1XxhG4tLgC6qUWEGpBSkCrgHZnfiJV+x3eSQJVx4MtH80e6wu0Vq7BZu8XWNv4CWrGOizW1mCu0obiBRuIZkG6IK5VQFiH5D54V0CqfhFaYOXKJqx3ei648zklcqngffDSn8GFCFiXu2C3r8LC4spQ4RN/Ax9ZoME6booX7SHBNyA+AD6ywNLyFZhfWnXegwqHP4t1T/hIAs5rvtIPbZi92IJ0Qex5f3am4ZwunvChBdyOEuugaBbMqFxod/l91j98PJRAwR5zHibOzcT29DqcydR9g8fDCqSIOTaMDftdOjh8/L8icDr9R2swdAEJm08Kgyc6fDsdHj4eRiBD+WFR8JMR4eNO5OqJWNBC2MhEFZhIRYdPoOpWUjYeCywwrVgPIWLMhZ35pHxjUxY4UvVqXKq6/zOEKozhgJxjT0vYPCZj/bjfJKfqxxNyLVKScv3lM5n6/aHhRzWqm6R+Az1Dmn8oNv4FAAAAAElFTkSuQmCC"
                decoding="async"
                style="position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%; object-fit: cover"
              />
            </div>
          </div>
          <span class="">优惠</span>
        </li>
        <li class="" data-analytics="button" data-label="客服">
          <i></i>
          <div style="cursor: inherit" class="image_imageContainer__2dKjX image_cover__3B5D- footer_icon__1Rh5j">
            <div style="display: block; overflow: hidden; position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; margin: 0px">
              <img
                class="footer_icon__1Rh5j"
                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAE/UlEQVRoge2ZXWgcVRTHt1rrByr1wRfRgoKQB1+0UuyDHw/Wl2JFiyhWBYUiKFZqDBTUJ8UXkT5aBEGpOJM11obeO/uV5N7ZbDYzm9UQajB17u7OzJ3dtGnzXVONTY7cbdJusjP7kcykBfOHw8IOM/z+55577pndUGhTm9qUr1L1sw+qmeIrVHcOqxn+Jc04X1C9+L6a4S8lfxu9N3QjigxObqcZ51OqOUNE4+AVVHcWqOb0qlrxPcOAW683dyibhVvUjNNKNT5eC9zVjOYUSD8/cN3gRTlQnSe1wVGwitNwfmIOiudm4Ux+ArKnz9Y10NXHARETMLWOEwK3bSi8OsgfFhm0SzOwsLgIF+fmoTR2EUbyE5AZqg+f6LPL8GgpMDE1Qia3b1y9687Ihck5EJq5+A84o7Mw9McYpLLFuvDxVfBo2QS14gBwc/AGdB61ijNl+OnZv+HPwiQkM05DdR9PWa7w6KoJ86tA4anuvKwNluDywiJMTgv4iYY3bay3NjwSQa2FGLEfCazjUI0beXsKxqcugfhUG8x8LNkAPFneDxYKxEBygO8XMKJ8cvYUDDTQaUREm4BHSxEjpRbfDVDNOS6ARKf59fdz9eH7bYgkmwNH11bhiK/wojtQjU8IsP7BUl34Ho2DsobMo6t7wUz7aoBozv2Nbtaefg6KukZwcvVcmPLVQHJgdFfD8HR98GgpstnSHU2DipvEFEl05xjN8IMEYKv4Xh0o7qsH393PRR/3BR6J6LUeKq8+GbsTEfOjnyLsWLuSexsAbnKFHx4e3kZ1fnoFmM6jALBluQN5wqdtf+HJlU4k4DExR8LYAAktB+t0L5PM6NOuU+NAcU8tA10C3kdwVGEAU/uwjFkF/JVox/mnqgyoeuk1VwO686GXgUTaFqen7/CobKDQIinGt6vhRcgo926VAaKVXnc3UGxzM7B6ovQzThETOmKFFhmz790MtGN2aF0GEqkA4btNkJERnIF4gPCd3fkyvBSUgVhvgPBd+RWQvhuIBgh/MrES3ncD0T6+Pyj4E4lcFaCvBiJJ3hZR7X2+w1MTTsTd4f0x0M8holrihG2LJvkuv+E7Yt7w0toMFF91mygxtQ4lUqUdfhroiNaGl5AB4URuh4TYN64HGWbvuBhwnijDpzlg9drpGqHObkJgK6LmjB+ZD0eqRwOpGnAxPAzb2hV20PV61HisyoCY8nrSPIorRgOsWl8vX8fU/NkLDFNLRUlzb62IJs294WhubxhdiXbMPvE2YIyVk0pgq4xZz8oxwjhaBV9hYotCrecV1W6NEP5M5TVM7be8s2s5oSYlYfaZ9yqwE5WJlbHxQrvCWqVo/snQWpVKnb8LEeuC5yoQ+/FGnyUSJWE25LkCCnszFIRER/IuI3Monea3N/IcGeeOeGYfG9MIWfcEYkAAYmoWapTSD/V+qBXlICN22TP7iH0cClKo19qJqXnJu9NYBqbWntX3dcbt+yTMfqzZfRCzTq3lXbhZ1drQnd2F5VLgMmaK2JAyYlkJsYU68H91KOajgcNfM2G9gak5Xwl/smsJvtnAxnw4wl4MbbQQsZ7F1BwV8L+4TJSNhIyNsXW1x/VK0cbv7kwUjsrY+Lc5cLYoIyZJMfZA6EaQmF1EB5ExO1MH/oKE2HdhxHaGblSJrMpK/jkZ5z6QFfa5jFmbrOQOhCPG7vBG/AuzqU39j/QfAdrRLU83fWwAAAAASUVORK5CYII="
                decoding="async"
                style="position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%; object-fit: cover"
              />
            </div>
          </div>
          <span class="">客服</span>
        </li>
        <li class="" data-analytics="button" data-label="赞助">
          <i></i>
          <div style="cursor: inherit" class="image_imageContainer__2dKjX image_cover__3B5D- footer_icon__1Rh5j">
            <div style="display: block; overflow: hidden; position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; margin: 0px">
              <img
                class="footer_icon__1Rh5j"
                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAESElEQVRoge2YT2gjVRjAo4J7EPGkIq4HEQQPihfBVe8qiIqeBA+exJPoxbMIiuJJWQQvenClaberrM6bSZqm75tp08wkbbe1pstu0mbevEmTNO12i+2mNm0+maFLp900mTeTtCvkg4+Qf+/9fm++mfnmRSL96Ec/2sa4YT8Nhv0l6PZlanDoeaY5yKopE2DfKWCfiwQNRLwPjNJn1ODbVOd4Ejk2yZGoDCVqukmANSUwf4jPVR4QFlAz/NuTAqc6x6QDDwfw3iTA4oLwpZfAsPeOTgIGx+xfFcxdX8X5azWcXVjBqfkKpmeWQ8JbSFqAH0qVfehbwK33I5MsFNZwp7GH2//u4vrGNpZXNrHIN9zPZ3JV1DJ2MPgURwk6wLtHwSz6FzDsmncS095AJ+rbDbyxUcfllU1csm7iQn4Np/+uYmq6FAh+NGX5gr+dMc18zK9A8/YkDmCz2cRb9QaWqptozJbdUgpb84kJMXjJOQoqf9aXgHciB3qr3sDq6lbgMrkTvvXJKnVIRePPCQvU1m7h6nrdLZduwI+MB4OXggpUalvIy/+4pSQKC4a9BQafp7q967yPh4CXggo4J/D14jrqs2UxeN2uqpnSE+54hv1GbNzaDQMvBRW4uriGc1drODEldqWBDP/YO6ZE2YVTEZjJVVG/Irj6hj2Zz+MZ75gytc8SMMsnLqBlxa48oNs0NVd5pNW4ClgvEDAXTlSAdlxtvgO6/RM17I/U6eVn/Iwva+xlmbJPCLBBt2E7TQEty9+NhAgC7JtTFVCNypNhBCTK3vcrMKwsdV+AZvj5oPByHs8QYFk/8IOk0CMBpynTra+CPDBJlMU6wxcxSvI4IOV7I+B0lDKwpkLZKyICBKxPO8H/SU2MkoIL3xOBhNMO709GgCX9wiPivYSaN9vCjxUxug/eE4ERpx32TEioafoVGNWrj7aD/2OseAi86wIj44fh949A1q9ALof3E2CNVvCXk63huyZwfEfJLkQEgoB5TQQ+vECaY0xr0w4DS3jHSE7yxwm1PpAo+1pS+TuJqRsPec8BCVjJ+//fE0tt4UMJjOkcY579mlbptASEss8VzXpVAvbj0RJxT1qwvlBU600C5q/e734b6QwfXCDNUVGDd5Cd8lJ80Rd8IAFn5eUOKx8mh2P+4YUFxtLuDao38GDixdjBDWqg2wLJtHXsNl83ckgRW/kBUQEiuF/jN53tw0FZfOUHRAV6UzYHTdn/SwDYnvN6tK+5qwUImDMErPdkrfzwxETtwYty4fWoVIjd9QLEAdest44bNyoXXwwjMtwrAQdcptbbvgYPITLsWwBYzSf4FRHw0CLxgrvT1zEImJf8gCPiPZEuxJCSPxcl+Xg7+ChZ9P28EUmmrKckMNd7De5XJCrnd0Yn+WsRkaC6fZZQ67wETCPAfnG6yF6BtxSRC99HpYI2pBR+pin7+Tt+1I9+9CPixH88983PRtjROwAAAABJRU5ErkJggg=="
                decoding="async"
                style="position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%; object-fit: cover"
              />
            </div>
          </div>
          <span class="">赞助</span>
        </li>
        <li class="" data-analytics="button" data-label="我的">
          <i></i>
          <div style="cursor: inherit" class="image_imageContainer__2dKjX image_cover__3B5D- footer_icon__1Rh5j">
            <div style="display: block; overflow: hidden; position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; margin: 0px">
              <img
                class="footer_icon__1Rh5j"
                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAEPUlEQVRoge2XT2gcVRzHp2gVKoIUBFvBgxdBvXhSg3gRKRVET4IKepOKCJ48eVAhCIIoEk+CByPsJE2KJe/NbppN3pvtZrOTTFMLbdXMbPa9nbfbNdsmsUla3dL85M0mm7HMrrM7O7MV8oPvZfYPn+/v/X6/9xtF2Y/92I+eRcasHdHz4j1qON+SvBijhpOkefEDzYvP9fnKiwBwj3I3BjXE89QQM9QQ2yTvQCtRQ9Sm58Qnplk5pNwNYZqVQzQvhttBezWdcwBTBpjySjLDX+orfHaxdpQaYjE4fAkwYYB2hCm/pdHS+/3LvA/84sU/QFQ3YPNGHeq3bsPGVh3OLghIzzqAKG/Ce4V1/kbsBojh/OQFl5DlHfA7lTXLvuBNUb6lZfiTscHr8+UBL/zsuTJUa1uwun7TVW31BhRK63Bp6SrkzlXaw5PdcmJabAaI4dC9qeIAE3/CldqmK4utQWZBuJ+dOVsKBI92lCT8hbgatzkqz19eAV6+7urCb7XmqUx2CI8ap/Bd5AaIIU54y+f35VVYKq7BZeta81mqC3jkiovoDcw7Q7ugsjkvLl11JftAPktm/CdNUBGy9lCkBqjhnPI2rxybUi68Hg4eEQaRTyOaF1Pe0Tl3vuJOml7AI2mAiOeiNWCI0TsvL42GB0e7kyhbfiJqA4PN1WDOkbdoz+ARZX9rFtwfrYEF5/jeUvZveI3yV00TDv6XsM6/aGEgp0Qdcp+fmSstI5+ywZR/GeQ/MOWzvvWv83eUOAJl+IctMrg2la081u63GnWOY8q3/e4A04SDsRiYvFB9ABFW9b1NCctjsvKIL7xefgZTtuI/ffhHSpyBdDaACfvL/yT4qkb5x/I70znnUUz5y0jnX8v939e0zsYB4IASd2DdeSvs5ME6Nyf6+Xqp6extWfvdjU1+elS78rDS72iUCUsGzjph60jn7/YFFgAOJDOlY5jwbzDl84jyMqbsJqasHjjzM/y2iu16AlubKrK5iuy0qlmfqZP205HCyzmNKC+EqfkJUgQVWZBoIRXZmTFcfLan4Nls7UFEeDpsw07MFFuCJ7wmsL0tT6Qn8HI/l7M9LPzp6WDwCa9wYSjUeE3lnMOIcDM0fLoLeLRrwhru2gAmfCws/M9nlruHRw2N4MKJjuGTuvNKWPhTU+HhE25jW9dHU87hzrJPuR4Gfnyy0BP4xF4pfRoYPpXlT4WBP5nqMTySsquEwL2BDCDKBruGT9oRwFuNXtDs14OWz0I38CPJKDJvecqoMBTwBPhG5/DRZT7RbGabBC2hXzuCx9HDJxo6GbSEvg8GXwQVxwIObg9g64NABlIZdgRTfq0d/ARhcrTFBq8i65fRS3BfIANuGelsoNXmKZeydhtlz+GxnR1P88eVbl7eMS29iSn7ClM+LIUIG5b7SdRSsf3jCLIHVWy91pf35f3YD+X/E/8AJUVb1128F3IAAAAASUVORK5CYII="
                decoding="async"
                style="position: absolute; top: 0px; left: 0px; bottom: 0px; right: 0px; box-sizing: border-box; padding: 0px; border: none; margin: auto; display: block; width: 0px; height: 0px; min-width: 100%; max-width: 100%; min-height: 100%; max-height: 100%; object-fit: cover"
              />
            </div>
          </div>
          <span class="">我的</span>
        </li>
      </ul>
    </div>
    <click-captcha
      :visible.sync="showCaptcha"
      :generate-url="generateUrl"
      :verify-url="verifyUrl"
      @success="onCaptchaSuccess"
    />
  </div>
</template>
<script>
import ClickCaptcha from './libs/ClickCaptcha';

export default {
  name: 'login',
  components: {
    ClickCaptcha
  },
  data() {
    return {
      registerInfo: {},
      loginInfo: {},
      index: 0,
      infoType: 0, //0 是登陆 1是注册
      psw1: true,
      psw2: true,
      psw3: true,
      pid: '',
      // Captcha related
      showCaptcha: false,
      captchaType: '', // 'login' or 'register'
      generateUrl: '/api/captcha/generate',
      verifyUrl: '/api/captcha/verify',
    };
  },
  created() {
    let that = this;
    var query = that.$route.query;
    if (query.type) {
      that.infoType = query.type;
    }
    if (query.pid) {
      that.pid = query.pid;
    }
    that.changIndex();
  },
  methods: {
    changPsw(name) {
      this[name] = !this[name];
    },
    changInfoType(type) {
      let that = this;
      if (that.infoType == type) {
        return;
      }
      that.infoType = type;
      that.changIndex();
      that.loginInfo = {};
      that.registerInfo = {};
      this.psw1 = true;
      this.psw2 = true;
      this.psw3 = true;
    },
    changIndex() {
      // Removed refresh call
      this.index = parseInt(20 * Math.random());
    },
    login() {
      let that = this;
      let info = that.loginInfo;
      if (!info.name || !info.password) {
        that.$parent.showTost(0, '请输入您的账号和密码！');
        return;
      }
      // Trigger captcha
      that.captchaType = 'login';
      that.showCaptcha = true;
    },
    doLogin() {
      let that = this;
      let info = that.loginInfo;
      that.$parent.showLoading();
      that.$apiFun.login(info).then(res => {
        if (res.code !== 200) {
          that.$parent.showTost(0, res.message);
          that.$parent.hideLoading();
        }
        if (res.code === 200) {
          sessionStorage.setItem('token', res.data.api_token);
          that.$store.commit('changToken');
          that.$parent.getUserInfo();
          that.$parent.openDaoTime();
          that.$parent.goNav('/');
        }
        that.$parent.hideLoading();
      });
    },
    register() {
      let that = this;
      let info = that.registerInfo;
      console.log(info);
      if (!info.name || info.name.length < 6) {
        that.$parent.showTost(0, ' 用户名长度6~16位，以字母或数字组合！');
        return;
      }
      if (!info.password || info.password.length < 6) {
        that.$parent.showTost(0, '请输入正确的密码长度，最少6位！');
        return;
      }
      if (!info.confirmPass || info.confirmPass != info.password) {
        that.$parent.showTost(0, '两次密码不一致！');
        return;
      }
      if (!info.realname || info.realname.length < 2) {
        that.$parent.showTost(0, '请输入您的真实姓名!');
        return;
      }
      if (!info.paypassword || info.paypassword.length < 6) {
        that.$parent.showTost(0, '请输入正确的支付密码长度，最少6位！');
        return;
      }
      
      // Trigger captcha
      that.captchaType = 'register';
      that.showCaptcha = true;
    },
    doRegister() {
      let that = this;
      let info = that.registerInfo;
      that.$parent.showLoading();
      if (that.pid) {
        info.pid = that.pid;
      }
      console.log(info)
      that.$apiFun.register(info).then(res => {
        that.$parent.showTost(1, res.message);
        if (res.code == 200) {
          sessionStorage.setItem('token', res.data.api_token);
          that.$store.commit('changToken');
          that.$parent.getUserInfo();
          that.$parent.openDaoTime();
          that.$parent.goNav('/');
        }
        that.$parent.hideLoading();
      });
    },
    onCaptchaSuccess() {
      if (this.captchaType === 'login') {
        this.doLogin();
      } else if (this.captchaType === 'register') {
        this.doRegister();
      }
    }
  },
  updated() {
    let that = this;
  },
  beforeDestroy() {},
};
</script>
<style lang="scss" scoped>
@import '../../static/css/79a97583f57023fe4fa8.css';
@import '../../static/css/438b8f8fe59c79885d4c.css';
@import '../../static/css/4186aeadfd68d65ea7ad.css';
#__next {
  width: 100vw;
  height: 100vh;
}
.entry_entryStyle__FXwIQ .entry_sponsorGroup__28DPX {
  background-size: 100% 100%;
  margin: 0.4rem auto;

  width: 10.94rem;
  height: 3rem;
}
.register_formContainer__3DhlE {
  overflow: visible;
}

.login_formContainer__2zhUW {
  width: 8.02rem;
}
// .login_formContainer__2zhUW {

//     width: 10.02rem;

// }
// .login_formContainer__2zhUW .login_normalContent__1I3Xg .login_normalLoginWrap__Q4P0O .login_inputBox__3j84z .login_inputGroup__1xPH8{
//   width: 100%;
//       height: 1.5rem;
// }
// .login_formContainer__2zhUW .login_normalContent__1I3Xg .login_normalLoginWrap__Q4P0O .login_inputBox__3j84z .login_inputGroup__1xPH8 .login_inputMain__18b4V .login_input__hMBpm{
//       height: 1.5rem;
//     line-height: 1.5rem;
//     font-size: 0.7rem;
// }
// .login_formContainer__2zhUW .login_normalContent__1I3Xg .login_title__1VIs3{
//  font-size: 0.66rem;
// }
// .login_formContainer__2zhUW .login_normalContent__1I3Xg .login_normalLoginWrap__Q4P0O .login_btnGroup__37-ja {
//     height: 1.2rem;
// }
</style>